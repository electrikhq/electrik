/**
 * Minimal WebAuthn helpers for laravel/passkeys JSON options.
 * Converts base64url challenge/credential fields for navigator.credentials.
 */
window.ElectrikPasskeys = (function () {
    function bufferToBase64Url(buffer) {
        const bytes = new Uint8Array(buffer);
        let str = '';
        bytes.forEach((b) => { str += String.fromCharCode(b); });
        return btoa(str).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
    }

    function base64UrlToBuffer(value) {
        const pad = '='.repeat((4 - (value.length % 4)) % 4);
        const base64 = (value + pad).replace(/-/g, '+').replace(/_/g, '/');
        const raw = atob(base64);
        const buffer = new ArrayBuffer(raw.length);
        const view = new Uint8Array(buffer);
        for (let i = 0; i < raw.length; i++) {
            view[i] = raw.charCodeAt(i);
        }
        return buffer;
    }

    function transformCreationOptions(options) {
        const publicKey = structuredClone(options);
        publicKey.challenge = base64UrlToBuffer(publicKey.challenge);
        publicKey.user.id = base64UrlToBuffer(publicKey.user.id);
        if (Array.isArray(publicKey.excludeCredentials)) {
            publicKey.excludeCredentials = publicKey.excludeCredentials.map((cred) => ({
                ...cred,
                id: base64UrlToBuffer(cred.id),
            }));
        }
        return publicKey;
    }

    function transformRequestOptions(options) {
        const publicKey = structuredClone(options);
        publicKey.challenge = base64UrlToBuffer(publicKey.challenge);
        if (Array.isArray(publicKey.allowCredentials)) {
            publicKey.allowCredentials = publicKey.allowCredentials.map((cred) => ({
                ...cred,
                id: base64UrlToBuffer(cred.id),
            }));
        }
        return publicKey;
    }

    function credentialToJson(credential) {
        const response = credential.response;
        const json = {
            id: credential.id,
            rawId: bufferToBase64Url(credential.rawId),
            type: credential.type,
            response: {},
        };

        if (response.clientDataJSON) {
            json.response.clientDataJSON = bufferToBase64Url(response.clientDataJSON);
        }
        if (response.attestationObject) {
            json.response.attestationObject = bufferToBase64Url(response.attestationObject);
        }
        if (response.authenticatorData) {
            json.response.authenticatorData = bufferToBase64Url(response.authenticatorData);
        }
        if (response.signature) {
            json.response.signature = bufferToBase64Url(response.signature);
        }
        if (response.userHandle) {
            json.response.userHandle = bufferToBase64Url(response.userHandle);
        }

        return json;
    }

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    /**
     * Map WebAuthn / network / server failures to short user-facing copy.
     * Never surface stack traces, file paths, or HTML error pages.
     */
    function friendlyError(error, fallback) {
        const fallbackMessage = fallback || 'Something went wrong. Please try again.';

        if (!error) {
            return fallbackMessage;
        }

        switch (error.name) {
            case 'AbortError':
            case 'NotAllowedError':
                return 'Passkey was cancelled or timed out.';
            case 'InvalidStateError':
                return 'This passkey cannot be used here. Try another device or authenticator.';
            case 'SecurityError':
                return 'Passkeys are blocked for this site address. Open the app using the same URL as APP_URL.';
            case 'NotSupportedError':
                return 'Passkeys are not supported in this browser.';
            case 'NetworkError':
                return 'Network error. Check your connection and try again.';
            default:
                break;
        }

        const message = String(error.message || '').trim();

        if (!message) {
            return fallbackMessage;
        }

        if (
            message.length > 160
            || /<!DOCTYPE|<html|stack trace|SQLSTATE|\/vendor\/|\/Users\/|TypeError|undefined is not|Exception| at |\{"message"/i.test(message)
        ) {
            return fallbackMessage;
        }

        return message;
    }

    function messageFromPayload(data, fallback) {
        if (!data || typeof data !== 'object') {
            return fallback;
        }

        const candidate = data.message
            || data.errors?.credential?.[0]
            || data.errors?.name?.[0]
            || (Array.isArray(data.errors) ? data.errors[0] : null);

        return friendlyError({ message: candidate }, fallback);
    }

    async function fetchJson(url, options = {}) {
        let response;

        try {
            response = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(options.headers || {}),
                },
                ...options,
            });
        } catch (error) {
            throw new Error(friendlyError(error, 'Network error. Check your connection and try again.'));
        }

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(messageFromPayload(data, 'Unable to complete passkey request.'));
        }

        return data;
    }

    async function register({ name, optionsUrl = '/user/passkeys/options', storeUrl = '/user/passkeys' } = {}) {
        if (!window.PublicKeyCredential) {
            throw new Error('Passkeys are not supported in this browser.');
        }

        try {
            const { options } = await fetchJson(optionsUrl, { method: 'GET' });
            const credential = await navigator.credentials.create({
                publicKey: transformCreationOptions(options),
            });

            if (!credential) {
                throw new Error('Passkey registration was cancelled.');
            }

            return await fetchJson(storeUrl, {
                method: 'POST',
                body: JSON.stringify({
                    name: name || 'Passkey',
                    credential: credentialToJson(credential),
                }),
            });
        } catch (error) {
            throw new Error(friendlyError(error, 'Unable to register passkey.'));
        }
    }

    async function login({
        optionsUrl = '/passkeys/login/options',
        loginUrl = '/passkeys/login',
        remember = false,
    } = {}) {
        if (!window.PublicKeyCredential) {
            throw new Error('Passkeys are not supported in this browser.');
        }

        try {
            const { options } = await fetchJson(optionsUrl, { method: 'GET' });
            const credential = await navigator.credentials.get({
                publicKey: transformRequestOptions(options),
            });

            if (!credential) {
                throw new Error('Passkey sign-in was cancelled.');
            }

            return await fetchJson(loginUrl, {
                method: 'POST',
                body: JSON.stringify({
                    credential: credentialToJson(credential),
                    remember: !!remember,
                }),
            });
        } catch (error) {
            throw new Error(friendlyError(error, 'Unable to sign in with passkey.'));
        }
    }

    return { register, login, friendlyError, bufferToBase64Url, base64UrlToBuffer };
})();
