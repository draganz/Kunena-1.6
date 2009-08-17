<?php
/**
 * @version		$Id:$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
?>
									<tr class="<?php echo ($this->current%2) ? 'row_even' : 'row_odd'; ?>">
										<td class="col1"><span><?php echo $this->thread->posts; ?></span> <?php echo JText::_('K_REPLIES'); ?></td>
										<td class="col2"><a href="#" ><img src="images/emoticons/default.gif" alt="Smiles" /></a></td>
										<td class="col3">
											<h4>
												<a href="/forum/77-general-talk-about-kunena/26536-another-user-is-bothering-me" title="As I don't know where to post this I do it here."><?php echo $this->thread->subject; ?></a>
											</h4>
											<div class="post_info">
												<div class="topic_post_time"><?php echo JText::_('K_POSTED'); ?> 9 <?php echo JText::_('K_HOURS'); ?>, 33 <?php echo JText::_('K_MINUTES'); ?> <?php echo JText::_('K_AGO'); ?></div>
												<div class="topic_author"><?php echo JText::_('K_BY'); ?> <a href="/community/profile?userid=1515" title="MartinRuthenberg">MartinRuthenberg</a></div>
												<div class="topic_category"><?php echo JText::_('K_CATEGORY'); ?>: <a href="/forum/77-general-talk-about-kunena" title="General Talk about Kunena">General Talk about Kunena</a></div>
												<div class="topic_views">(<?php echo JText::_('K_VIEWS'); ?>: 28)</div>
											</div>
										</td>
										<td class="col4">
												<span class="topic_latest_post_avatar"><a href="/community/profile?userid=634" title="MartinRuthenberg"><img class="avatar" src="images/no_photo_sm.jpg" alt="<?php echo JText::_('K_NO_PHOTO'); ?>" /></a>
												</span>
												<span class="topic_latest_post">
													<?php echo JText::_('K_LAST_POST_BY'); ?> <a class="topic_latest_post_user" href="/community/profile?userid=634" title="<?php echo JText::_('K_POST'); ?> 810">810</a>
												</span>
												<span class="topic_time">16 <?php echo JText::_('K_MINUTES'); ?> <?php echo JText::_('K_AGO'); ?></span>
										</td>
									</tr>