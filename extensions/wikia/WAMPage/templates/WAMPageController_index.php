<ul class="wam-tabs">
	<li>
		<a href="#">1</a>
	</li>
	<li>
		<a href="#">2</a>
	</li>
	<li>
		<a href="#">3</a>
	</li>
	<li>
		<a href="#">4</a>
	</li>
	<li>
		<a href="#">5</a>
	</li>
	<li>
		<a href="#">6</a>
	</li>
</ul>
<div class="wam-header">
	<div class="wam-cards">
		<? foreach($visualizationWikis as $wiki) { ?>
			<a href="<?= $wiki['url'] ?>" class="wam-card">
				<figure class="wam-img">
					<img src="<?= $wiki['wiki_image'] ?>" alt="<?= $wiki['title'] ?>" title="<?= $wiki['title'] ?>" />
					<span class="wam-img-info"><?= $wiki['title'] ?></span>
				</figure>
				<div class="wam-score"><?= $wiki['wam'] ?></div>
				<span class="wam-vertical"><?= $wiki['hub_id'] ?></span>
			</a>
		<? } // end foreach ?>
	</div>
	
    <h2><?= wfMessage('wampage-header')->text(); ?></h2>
</div>

<div class="wam-progressbar"></div>
<div class="wam-content">
	<?= wfMessage(
		'wampage-content',
		$faqPage
	)->parse(); ?>
</div>

<div class="wam-index">
	<table>
		<tr>
			<th><?= wfMessage('wam-index-header-rank')->text() ?></th>
			<th><?= wfMessage('wam-index-header-score')->text() ?></th>
			<th><?= wfMessage('wam-index-header-wiki-name')->text() ?></th>
			<th><?= wfMessage('wam-index-header-vertical')->text() ?></th>
			<th><?= wfMessage('wam-index-header-vertical-rank')->text() ?></th>
			<th><?= wfMessage('wam-index-header-peak-rank')->text() ?></th>
		</tr>
		<tr>
			<td>1.</td>
			<td>99.91</td>
			<td><a href="http://www.wikia.com">runescape.wikia.com</a></td>
			<td>Video Games</td>
			<td>1</td>
			<td>1</td>
		</tr>
		<tr>
			<td>2.</td>
			<td>99.91</td>
			<td><a href="http://www.wikia.com">runescape.wikia.com</a></td>
			<td>Video Games</td>
			<td>1</td>
			<td>1</td>
		</tr>
	</table>
</div>