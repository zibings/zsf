<?php

	/**
	 * @var Stoic\Web\PageHelper $page
	 * @var string $token
	 */

?>
<?php $this->layout('shared::email', ['page' => $page]) ?>

								<!-- HERO IMAGE -->
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td>
											<!-- COPY -->
											<table width="100%" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td align="center" style="font-size: 25px; font-family: Helvetica, Arial, sans-serif; color: #333333; padding-top: 30px;" class="padding">Confirm your email for your WarBanner account</td>
												</tr>
												<tr>
													<td align="center" style="padding: 20px 0 0 0; font-size: 16px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #666666;" class="padding">Click the button below to confirm your email address and finish setting up your WarBanner user account.</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td align="center">
											<!-- BULLETPROOF BUTTON -->
											<table width="100%" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td align="center" style="padding-top: 25px;" class="padding">
														<table border="0" cellspacing="0" cellpadding="0" class="mobile-button-container">
															<tr>
																<td align="center" style="border-radius: 3px;" bgcolor="#256F9C"><a href="<?=$page->getAssetPath('~/confirm-email.php', ['token' => $token], true)?>" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff!important; text-decoration: none; border-radius: 3px; padding: 15px 25px; border: 1px solid #256F9C; display: inline-block;" class="mobile-button">Confirm &rarr;</a></td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>