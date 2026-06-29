<?php
$showSidebar = $showSidebar ?? true;
?>

<?= $this->include('layout/header'); ?>
<?php if ($showSidebar): ?>
  <?= $this->include('layout/sidebar'); ?>
<?php endif; ?>

<main class="app-main">
  <?= $this->renderSection('content'); ?>
</main>

<?= $this->include('layout/footer'); ?>
