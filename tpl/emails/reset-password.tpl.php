<?php

	/**
	 * @var Stoic\Web\PageHelper $page
	 * @var string $token
	 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title><?=$page->getTitle()?></title>

		<style type="text/css">
			body {
				margin: 0;
				padding: 0;
				background-color: #333333;
				color: #777777;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 12px;
				-webkit-text-size-adjust: none;
				-ms-text-size-adjust: none;
			}

			h1, h2 {
				color: #3b5167;
				margin-bottom: 10px !important;
			}

			a, a:link, a:visited {
				color: #5187bd;
				text-decoration: none;
			}

			a:hover, a:active {
				text-decoration: none;
				color: #2868a9 !important;
			}

			p {
				margin: 0 0 14px 0;
			}

			img {
				border: 0;
			}

			table td {
				border-collapse: collapse;
			}

			.highlighted {
				background-color: #ffe69e;
				color: #3b5167;
				padding: 2px 4px;
				border-radius: 2px;
				-moz-border-radius: 2px;
				-webkit-border-radius: 2px;
			}

			/*Hotmail and Yahoo specific code*/
			.ReadMsgBody {width: 100%;}

			.ExternalClass {width: 100%;}

			.yshortcuts {color: #5187bd;}

			.yshortcuts a span {color: #5187bd; border-bottom: none !important; background: none !important;}

			/*Hotmail and Yahoo specific code*/
		</style>

		<!--[if gte mso 9]>
		<style type="text/css">
			#pageContainer {
				background-color: transparent !important;
			}

			td.list_td {
				line-height: 11pt !important;
			}

			td.list_image {
				vertical-align: middle;
			}

			td.footer_list {
				padding-bottom: 0px !important;
				line-height: 15pt !important;
			}

			td.footer_list_image {
				vertical-align: middle !important;
				padding-bottom: 0px !important;
				padding-bottom: 2px !important;
			}

			td.socialIcons {
				padding-top: 20px !important;
			}
		</style>
		<![endif]-->
	</head>

	<body>
		<table id="pageContainer" width="100%" align="center" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; background-repeat:repeat; background-color:#333333;">
			<tr>
				<td style="padding:30px 20px 40px 20px;">
					<!-- Start of logo, date, forward  and home links container -->
					<table width="600" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; text-align:left; font-family:Arial, Helvetica, sans-serif; font-weight:normal; font-size:12px; line-height:15pt; color:#777777;">
						<tr>
							<td bgcolor="#5187bd" colspan="2" height="7" style="font-size:2px; line-height:0px;">&nbsp;</td>
						</tr>
						<tr>
							<td bgcolor="#ffffff" width="510" valign="middle" style="padding:40px 30px 35px 30px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:100%; color:#5187bd;">
								<!-- <img alt="Logo" src="images/logo.png" align="left" border="0" vspace="0" hspace="0" style="display:block;" /> -->
								<h1>ZSF - <?=$page->getTitle()?></h1>
							</td>
						</tr>
						<tr>
							<td colspan="2" height="11" style="font-size:2px; line-height:0px;">
								&nbsp;
							</td>
						</tr>
					</table>
					<!-- End of logo, date, forward  and home links container -->

					<!-- Start of text banner -->
					<table bgcolor="#ffffff" width="600" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; text-align:left; font-family:Arial, Helvetica, sans-serif; font-weight:normal; font-size:12px; line-height:15pt; color:#777777;">
						<tr>
							<td height="11" style="font-size:2px; line-height:0px;">
								&nbsp;
							</td>
						</tr>
					</table>
					<!-- End of text banner -->

					<!-- Start of content with author bio -->
					<table bgcolor="#ffffff" width="600" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; text-align:left; font-family:Arial, Helvetica, sans-serif; font-weight:normal; font-size:12px; line-height:15pt; color:#777777;">
						<tr>
							<td style="padding-top:30px; padding-right:30px; padding-left:30px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:100%; color:#aaaaaa;">
								&nbsp;
							</td>
						</tr>
						<tr>
							<td style="padding-right:30px; padding-left:30px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#777777;">
								<p style="font-family:'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:36px; line-height:30pt; color:#3b5167; font-weight:300; margin-top:0; margin-bottom:20px !important; padding:0; text-indent:-3px;">
									Password Reset Request
								</p>
							</td>
						</tr>
						<tr>
							<td style="padding-right:30px; padding-bottom:30px; padding-left:30px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#777777;">
								Someone (hopefully you) requested a password reset on your account.  Please click the link below to continue the process:

								<table width="540" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border-spacing:0;">
									<tr>
										<td style="padding-top:20px; text-align: center">
											<a href="<?=$page->getAssetPath('~/reset-password.php', ['token' => $token], true)?>" style="background-color: #777777; color: #FFFFFF; padding: 5px; border-radius: 4px">Reset Password</a>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td height="11" style="font-size:2px; line-height:0px;">
								&nbsp;
							</td>
						</tr>
					</table>
					<!-- End of content with author bio -->

					<!-- Start of footer -->
					<table bgcolor="#f4f4f4" width="600" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; text-align:left; font-family:Arial, Helvetica, sans-serif; font-weight:normal; font-size:12px; line-height:15pt; color:#777777;">
						<tr>
							<td>
								<table width="600" cellpadding="0" cellspacing="0" style="border-collapse:collapse; text-align:left; font-family:Arial, Helvetica, sans-serif; font-weight:normal; font-size:12px; line-height:15pt; color:#777777;">
									<tr>
										<td width="30">&nbsp;</td>
										<td width="160" valign="top" style="padding-top:30px; padding-bottom:30px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#777777;">
											Copyright &copy; <?=date('Y')?> ZSF<br />
											All Rights Reserved.
										</td>
										<td width="30">
											&nbsp;
										</td>
										<td width="160" valign="top" style="padding-top:34px; padding-bottom:30px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#777777;">
											<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; text-align:left; font-family:Arial, Helvetica, sans-serif; font-weight:normal; font-size:12px; line-height:100%; color:#777777;">
												<tr>
													<td class="footer_list" width="140" valign="top" style="padding:0 0 9px 0; line-height:9pt;">
														<a href="#" style="text-decoration:underline; color:#5187bd; line-height:9pt;">www.website.com</a>
													</td>
												</tr>
												<tr>
													<td class="footer_list" width="140" valign="top" style="padding:0 0 9px 0; line-height:9pt;">
														<a href="mailto:" style="text-decoration:underline; color:#5187bd; line-height:9pt;">email@website.com</a>
													</td>
												</tr>
												<tr>
													<td class="footer_list" width="140" valign="top" style="padding:0 0 9px 0; line-height:9pt;">
														+1 234 5678 910
													</td>
												</tr>
											</table>
										</td>
										<td width="30">
											&nbsp;
										</td>
										<td width="160" valign="top" style="padding-top:30px; padding-bottom:30px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#777777;">
											<strong>Why receiving this email?</strong><br />
											Because a password reset was requested for your account.
										</td>
										<td width="30">
											&nbsp;
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td bgcolor="#5187bd" height="7" style="font-size:2px; line-height:0px;">
								&nbsp;
							</td>
						</tr>
					</table>
					<!-- End of footer -->

				</td>
			</tr>
		</table>

	</body>
</html>
