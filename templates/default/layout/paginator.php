<?php

	if(!empty($data) && $data['pages'] > 1)
	{
		echo '<p class="paginator">' . $x7->lang('page') . ': ';
		
		$last_page = 0;
		for($page = 1; $page <= $data['pages']; $page++)
		{
			if($page == 1 || $page == $data['pages'] || ($page >= $data['page']-1 && $page <= $data['page']+1))
			{
				if($last_page != $page-1)
				{
					echo '...';
					echo ' &nbsp;';
				}
				
				if($page == $data['page'])
				{
					echo "<a href='{$data['action']}{$page}' data-href='{$data['action']}{$page}' class='page_link current_page'>{$page}</a>";
				}
				else
				{
					echo "<a href='{$data['action']}{$page}' data-href='{$data['action']}{$page}' class='page_link'>[{$page}]</a>";
				}
			
				echo ' &nbsp;';
				
				$last_page = $page;
			}
		}
		
		echo '</p>';
	}