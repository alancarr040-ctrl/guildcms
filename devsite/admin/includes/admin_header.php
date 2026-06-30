<?php
declare(strict_types=1);

$page_title = $page_title ?? 'Admin Dashboard';
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <title><?= admin_h($page_title) ?> - The Guild CMS Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/admin/">The Guild CMS Admin</a>

        <div class="navbar-nav">
            <a class="nav-link" href="/admin/">Dashboard</a>
            <a class="nav-link" href="/admin/?page=audit_log">Audit Log</a>
			<a class="nav-link" href="/admin/?page=development">Dev Center</a>
            <a class="nav-link" href="/">View Site</a>
        </div>

        <span class="navbar-text ms-auto">
            Logged in as <?= admin_h($admin_user['username']) ?>
        </span>
    </div>
</nav>

<main class="container-fluid">