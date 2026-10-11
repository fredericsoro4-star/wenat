<script>
  // Variables PHP injectées pour le JS
  const lapinsData = <?= json_encode($lapins_js ?? []) ?>;
  const SESSION_USER = {
    user_id:    <?= json_encode($_SESSION['user']['id']     ?? null) ?>,
    first_name: <?= json_encode($_SESSION['user']['nom']    ?? null) ?>,
    last_name:  <?= json_encode($_SESSION['user']['prenom'] ?? null) ?>,
    email:      <?= json_encode($_SESSION['user']['email']  ?? null) ?>,
    phone:      <?= json_encode($_SESSION['user']['numero'] ?? null) ?>
  };
</script>
