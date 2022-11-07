const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');


/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/vendor/**/*.blade.php',
		'./vendor/neerajsohal/slate/resources/views/components/**/*.blade.php',
		'./vendor/usernotnull/tall-toasts/config/**/*.php',
    	'./vendor/usernotnull/tall-toasts/resources/views/**/*.blade.php',
		'./vendor/rappasoft/laravel-livewire-tables/resources/views/**/*.blade.php',
		'./vendor/wire-elements/modal/resources/views/**/*.blade.php',
		'./vendor/electrik/electrik/resources/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
			colors: {
				primary: colors.indigo,
				secondary: colors.stone,
				success: colors.green,
				warning: colors.yellow,
				danger: colors.red,
				info: colors.blue,
				transparent: colors.transparent,
			},
			maxWidth: {},
        },
    },

    plugins: [
		require('@tailwindcss/forms'),
		require('@tailwindcss/typography'),
	],
};
