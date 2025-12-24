<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home (Dashboard)
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('dashboard.index'));
});

// Dashboard
Breadcrumbs::for('dashboard.index', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard.index'));
});

// Teams
Breadcrumbs::for('teams.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Teams', route('teams.index'));
});

Breadcrumbs::for('teams.create', function (BreadcrumbTrail $trail) {
    $trail->parent('teams.index');
    $trail->push('Create Team', route('teams.create'));
});

Breadcrumbs::for('teams.settings', function (BreadcrumbTrail $trail, $team) {
    $trail->parent('teams.index');
    $trail->push('Team Settings', route('teams.settings', $team));
});

Breadcrumbs::for('teams.members.index', function (BreadcrumbTrail $trail, $team) {
    $trail->parent('teams.settings', $team);
    $trail->push('Members', route('teams.members.index', $team));
});

Breadcrumbs::for('teams.members.invite', function (BreadcrumbTrail $trail, $team) {
    $trail->parent('teams.members.index', $team);
    $trail->push('Invite Member', route('teams.members.invite', $team));
});

// Billing
Breadcrumbs::for('billing.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Billing', route('billing.index'));
});

Breadcrumbs::for('billing.plans', function (BreadcrumbTrail $trail) {
    $trail->parent('billing.index');
    $trail->push('Plans', route('billing.plans'));
});

Breadcrumbs::for('billing.subscription', function (BreadcrumbTrail $trail) {
    $trail->parent('billing.index');
    $trail->push('Subscription', route('billing.subscription'));
});

Breadcrumbs::for('billing.payment-methods', function (BreadcrumbTrail $trail) {
    $trail->parent('billing.index');
    $trail->push('Payment Methods', route('billing.payment-methods'));
});

Breadcrumbs::for('billing.address', function (BreadcrumbTrail $trail) {
    $trail->parent('billing.index');
    $trail->push('Billing Address', route('billing.address'));
});

Breadcrumbs::for('billing.invoices', function (BreadcrumbTrail $trail) {
    $trail->parent('billing.index');
    $trail->push('Invoices', route('billing.invoices'));
});

// Settings
Breadcrumbs::for('settings.profile', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Profile', route('settings.profile'));
});

Breadcrumbs::for('settings.security', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Security', route('settings.security'));
});

