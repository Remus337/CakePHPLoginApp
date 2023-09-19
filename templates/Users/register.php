<div class="column-responsive column-80">
    <div class="users form content">
        <h2>Registration</h2>
        <?= $this->Form->create($user); ?>
            <?= $this->Form->control('name'); ?>
            <?= $this->Form->control('email'); ?>
            <?= $this->Form->control('password'); ?>
            <?= $this->Form->submit('Register', array('class="button"')); ?>
        <?= $this->Form->end(); ?>
    </div>
</div>
