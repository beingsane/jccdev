<?php
/**
 * @package     JCCDEV
 * @subpackage  Views
 *
 * @copyright  	Copyright (C) 2014, Tilo-Lars Flasche. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
?>

<style>
div.buttonbox {
	border-radius: 5px;
	color: #ffffff;
	font-seize: 20px;
	font-weight: 700;
	height: 50px;
	padding: 10px;
	width: 50px;
}
</style>

<div id="j-sidebar-container" class="span3">
	<?php echo JLayoutHelper::render("sidebar", array("active" => "JCCDEV"), JCCDEVLAYOUTS); ?>
</div>

<div id="j-main-container" class="span9">
	<div class="row-fluid">
		<h2>JCCDEV</h2>
		<div class="span3 buttonbox" style="background-color:#000000">
			<?php echo JText::_("COM_JCCDEV_COMPONENTS"); ?>
		</div>
		<div class="span3 buttonbox" style="background-color:#ff0000">
			<?php echo JText::_("COM_JCCDEV_MODULES"); ?>
		</div>
		<div class="span3 buttonbox" style="background-color:#0000ff">
			<?php echo JText::_("COM_JCCDEV_TEMPLATES"); ?>
		</div>
		<div class="span3 buttonbox" style="background-color:#ff00ff">
			<?php echo JText::_("COM_JCCDEV_PLUGINS"); ?>
		</div>
	</div>
	<p>&nbsp;</p>
	<div class="row-fluid">
		<div class="span12">
		<h3><?php echo JText::_("COM_JCCDEV_JCCDEV_VERSION_CHECK") ?></h3>
		<?php if (null !== $item = JCCDEVHelper::getUpdate()) : ?>
			<div class="alert alert-info">
				<b>JCCDEV Update:</b> Version <?php echo $item->version; ?> is now available.
				<a href="<?php echo JRoute::_("index.php?option=com_installer&view=update") ?>"><button class="btn btn-primary">Update</button></a>
			</div>
		<?php else: ?>		
			<div class="alert alert-success">Latest JCCDEV version is installed.</div>
		<?php endif; ?>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12">
			<h3><?php echo JText::_("COM_JCCDEV_JCCDEV_HEADLINE_ARCHIVES") ?></h3>
			<?php if (empty($this->archives)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
			<?php else : ?>
				<table class="table table-striped">
					<thead>
						<tr>
							<td>Num</td>
							<td>Archive</td>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($this->archives as $i => $item) : ?>
						<tr>
							<td><?php echo $i + 1; ?></td>					
							<td><a href="<?php echo $item->link; ?>"><?php echo $item->name; ?></a></td>					
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
<!--
		<div class="span6">
			<h2><?php echo JText::_("COM_JCCDEV_JCCDEV_HEADLINE_NEWS") ?></h2>
			<?php echo JHtml::_('bootstrap.startAccordion', 'collapseTypes', array('active' => 'slide1')); ?>
				<?php echo JHtml::_('bootstrap.addSlide', 'collapseTypes', 'First Slide', 'collapse1'); ?>
				<p>First slide</p>
				<?php echo JHtml::_('bootstrap.endSlide'); ?>
				<?php echo JHtml::_('bootstrap.addSlide', 'collapseTypes', '2nd Slide', 'collapse2'); ?>
				<p>Second slide</p>
				<?php echo JHtml::_('bootstrap.endSlide'); ?>
			<?php echo JHtml::_('bootstrap.endAccordion'); ?>
		</div>
-->
	</div>
	<div>
		<input type="hidden" name="task" value=" " />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</div>