<?= $this->include('layout/header'); ?>
<?= $this->include('layout/sidebar'); ?>

<main class="app-main">
  <?= $this->renderSection('content'); ?>
</main>

<?= $this->include('layout/footer'); ?>