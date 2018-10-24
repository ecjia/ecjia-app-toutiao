<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia-merchant.dwt.php"} -->
<!-- {block name="footer"} -->
<script type="text/javascript">
	// ecjia.merchant.menu.init();
</script>
<!-- {/block} -->
<!-- {block name="home-content"} -->
<div class="page-header">
	<div class="pull-left">
		<h2>{if $ur_here}{$ur_here}{/if}</h2>
	</div>
	<div class="pull-right">
		<a  class="btn btn-primary" href="{$action_link.href}" id="sticky_a"><i class="fa fa-plus"></i> {$action_link.text}</a>
	</div>
	<div class="clearfix"></div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-body panel-body-small">
				<ul class="nav nav-pills pull-left">
					<li class="{if $type eq ''}active{/if}">
						<a class="data-pjax" href='{url path="toutiao/merchant/init"}'>今日发送 <span class="badge badge-info">{$type_count.send}</span> </a>
					</li>
					<li class="{if $type eq 'history'}active{/if}">
						<a class="data-pjax" href='{url path="toutiao/merchant/init" args="type=history"}'>历史发送 <span class="badge badge-info">{$type_count.history}</span> </a>
					</li>
					<li class="{if $type eq 'media'}active{/if}">
						<a class="data-pjax" href='{url path="toutiao/merchant/init" args="type=media"}'>图文素材 <span class="badge badge-info">{$type_count.media}</span> </a>
					</li>
				</ul>
			</div>
			<div class="panel-body panel-body-small">
				<section class="panel">
					<table class="table table-striped table-hover table-hide-edit">
						<thead>
							<tr>
								<th class="w250">今日热点主图</th>
								<th>内容标题</th>
								<th class="w200">发布时间</th>
							</tr>
						</thead>
						<!-- {foreach from=$list.item item=item key=key} -->
						<tr>
							<td>{$item.parent_img}</td>
							<td>{$item.title}</td>
							<td>{$item.create_time}</td>
						</tr>
						<!-- {foreachelse} -->
						<tr><td class="no-records" colspan="3">{lang key='system::system.no_records'}</td></tr>
						<!-- {/foreach} -->
					</table>
				</section>
				<!-- {$list.page} -->
			</div>
		</div>
	</div>
</div>
<!-- {/block} -->
