<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Electrik\Models\Role;
use Electrik\Models\Team;

Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});

Breadcrumbs::for('teams.index', function (BreadcrumbTrail $trail) {
    $trail->push('Teams', route('teams.index'));
});

Breadcrumbs::for('teams.create', function (BreadcrumbTrail $trail) {
    $trail->parent('teams.index');
    $trail->push('Create', route('teams.create'));
});

Breadcrumbs::for('teams.settings', function (BreadcrumbTrail $trail, Team $team) {
    $trail->parent('teams.index');
    $trail->push($team->name, route('teams.members', $team));
    $trail->push('Settings', route('teams.settings', $team));
});

Breadcrumbs::for('teams.members', function (BreadcrumbTrail $trail, Team $team) {
    $trail->parent('teams.index');
    $trail->push($team->name, route('teams.members', $team));
});

Breadcrumbs::for('teams.members.invite', function (BreadcrumbTrail $trail, Team $team) {
    $trail->parent('teams.members', $team);
    $trail->push('Invite', route('teams.members.invite', $team));
});

Breadcrumbs::for('teams.roles.index', function (BreadcrumbTrail $trail, Team $team) {
    $trail->parent('teams.members', $team);
    $trail->push('Roles', route('teams.roles.index', $team));
});

Breadcrumbs::for('teams.roles.create', function (BreadcrumbTrail $trail, Team $team) {
    $trail->parent('teams.roles.index', $team);
    $trail->push('Create', route('teams.roles.create', $team));
});

Breadcrumbs::for('teams.roles.edit', function (BreadcrumbTrail $trail, Team $team, Role $role) {
    $trail->parent('teams.roles.index', $team);
    $trail->push($role->display_name, route('teams.roles.edit', [$team, $role]));
});

Breadcrumbs::for('teams.permissions.index', function (BreadcrumbTrail $trail, Team $team) {
    $trail->parent('teams.members', $team);
    $trail->push('Permissions', route('teams.permissions.index', $team));
});

Breadcrumbs::for('billing.index', function (BreadcrumbTrail $trail) {
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
    $trail->push('Payment methods', route('billing.payment-methods'));
});

Breadcrumbs::for('billing.address', function (BreadcrumbTrail $trail) {
    $trail->parent('billing.index');
    $trail->push('Address', route('billing.address'));
});

Breadcrumbs::for('billing.invoices', function (BreadcrumbTrail $trail) {
    $trail->parent('billing.index');
    $trail->push('Invoices', route('billing.invoices'));
});

Breadcrumbs::for('settings.profile', function (BreadcrumbTrail $trail) {
    $trail->push('Account', route('settings.profile'));
    $trail->push('Profile', route('settings.profile'));
});

Breadcrumbs::for('settings.security', function (BreadcrumbTrail $trail) {
    $trail->push('Account', route('settings.profile'));
    $trail->push('Security', route('settings.security'));
});
