<form class="standard_form" data-action="do_admin_save_word_filter">
	<?php if($val('filter.id')): ?>
		<input type="hidden" name="id" value="<?php $esc($val('filter.id')); ?>" />
	<?php endif; ?>

	<label for="word"><?php $lang('word_filter_word'); ?></label>
	<input type="text" name="word" id="word" value="<?php $esc($val('filter.word')); ?>" />
	<p><?php $lang('word_filter_word_instr'); ?></p>
	
	<label for="replacement""><?php $lang('word_filter_replacement'); ?></label>
	<input type="text" name="replacement" id="replacement" value="<?php $esc($val('filter.replacement')); ?>" />
	<p><?php $lang('word_filter_replacement_instr'); ?></p>
	
	<label for="whole_word_only"><?php $lang('word_filter_whole_word'); ?></label>
	<input type="checkbox" name="whole_word_only" id="whole_word_only" <?php if($val('room.whole_word_only')) echo "checked"; ?> />
	<p><?php $lang('word_filter_whole_word_instr'); ?></p>
	
	<input type="submit" value="<?php $lang('word_filter_save_button'); ?>" />
</form>