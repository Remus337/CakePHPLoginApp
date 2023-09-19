<div class="column-responsive column-80">
    <div class="users form content">
        <h2>Login</h2>
        <?= $this->Form->create(); ?>
        <?= $this->Form->control('email'); ?>
        <?= $this->Form->control('password'); ?>
        <div class="g-recaptcha" name="g-recaptcha-response" data-sitekey="<?= $recaptcha_user ?>"></div>
        <?= $this->Form->submit('Login', ['class' => 'button']); ?>
        <?= $this->Form->end(); ?>
    </div>
</div>