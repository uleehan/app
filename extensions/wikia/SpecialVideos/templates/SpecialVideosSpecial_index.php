<?
/* @var $sortingOptions array */
/* @var $sortMsg string */
/* @var $wg object */
/* @var $isRemovalAllowed bool */
/* @var $showAddVideoBtn bool */
/* @var $pagination string */
?>
<ul class="special-videos-grid small-block-grid-3 large-block-grid-3 x-large-block-grid-4">
	<?php $counter = 0 ?>
	<?php foreach( $videos as $video ): ?>
		<?php $alpha = $counter % 3 == 0 ? ' alpha' : ''; ?>

		<li itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
			<?= $video['thumbnail'] ?>
			<div class="info">
				<p class="title">
					<a href="<?= Sanitizer::encodeAttribute( $video['fileUrl'] ); ?>" title="<?= Sanitizer::encodeAttribute( $video['title'] ); ?>"><?= htmlspecialchars( $video['title'] ); ?></a>
				</p>
				<p class="by-views">
					<?= $video['byUserMsg'] ?>
				</p>
				<div class="posted-in">
					<? if ( count($video['truncatedList']) ): ?>
						<?= wfMessage( 'specialvideos-posted-in-label' )->escaped(); ?>
						<ul>
							<? foreach( $video['truncatedList'] as $article ): ?>
								<li><a href="<?= Sanitizer::encodeAttribute( $article['url'] ); ?>"><?= htmlspecialchars( $article['titleText'] ); ?></a></li>
							<? endforeach; ?>
						</ul>
					<? endif; ?>
				</div>
			</div>
			<? if($isRemovalAllowed): ?>
				<a class="remove">
					<img class="sprite trash" src="<?= wfBlankImgUrl() ?>" title="<?= wfMessage( 'specialvideos-remove-modal-title' )->escaped(); ?>">
				</a>
			<? endif; ?>
		</li>

		<?php $counter++; ?>
	<?php endforeach; ?>
	<?php if (!empty($addVideo)): ?>
		<?php $alpha = $counter % 3 == 0 ? 'alpha' : ''; ?>

		<!-- Check user permissions, only admins may upload videos, hide element for non-admins -->
		<? if ($showAddVideoBtn): ?>
			<li class="add-video">
				<div class="add-video-placeholder addVideo"></div>
					<p><a href="#" class="addVideo"><?= wfMessage('special-videos-add-video' )->escaped(); ?></a></p>
			</li>
		<? endif; ?>
		<?php endif; ?>
</ul>
<?= $pagination ?>
<div class="errorWhileLoading messageHolder"><?=wfMessage( 'videos-error-while-loading' )->escaped(); ?></div>
