<?php
/*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2008  CAcert Inc.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<p><?= _( "Due to the increasing number of people that haven't been able to recover their passwords via the lost password form, there are now two other options available. 1.) If you don't care about your account you can signup under a new account and file dispute forms to recover your email accounts and domains. 2.) If you would like to recover your password via help from support staff, this requires a small payment to cover a real person's time to verify your claim of ownership on an account.  After you pay the required fee you will have to contact the proper person to arrange the verification. Click the payment button below to continue." ) . " " ?><? printf( _( "Alternatively visit our %sinformation page%s on this subject for more details." ), "<a href='http://wiki.cacert.org/wiki/FAQ/LostPasswordOrAccount'>", "</a>" ) ?></p>
<h3><?php echo _( "Password Recovery" ) ?></h3>
<h4><?php echo _( "Are you sure that you have attempted ALL of the following steps before completing the form below?" ) ?></h4>
<ol>
    <li><?php echo _( "Have you installed a CAcert client certificate in any of your browsers?" ) ?>
        <ul>
            <li><?php echo _( "If you have a certificate installed, you can log in that way, and then change your password" ) ?></li>
            <li><?php echo _( "Have you checked all of your browsers for an installed certificate?" ) ?></li>
        </ul>
    </li>
    <li><?php echo _( "Do you remember your 'secret' questions and answers?" ) ?>
        <ul>
            <li><?php echo _( "When you registered, did you use your correct Date of Birth?" ) ?>
                <ul>
                    <li><?php echo _( "You MUST enter both the same Date of Birth, AND your Main E-Mail address registered with CAcert." ) ?></li>
                    <li><?php echo _( "Remember that your questions and answers are case sensitive.  Spaces are significant and so are punctuation marks." ) ?></li>
                </ul>
            </li>
            <li><?php echo _( "If you think that you remember everything, fill out the form at the bottom of this page, and continue." ) ?></li>
        </ul>
    </li>
    <li><?php echo _( "Are you willing to create a new account?" ) ?>
        <ul>
            <li><?php echo _( "The new account must be created with a different e-mail address than you used before." ) ?></li>
            <li><?php echo _( "Once your new account has been created, go to the Dispute section of the menu." ) ?>
                <ul>
                    <li><?php echo _( "File a Dispute for any Domain names that you have registered." ) ?></li>
                    <li><?php echo _( "Each Dispute will ask you to confirm that you want to delete that Domain name." ) ?></li>
                    <li><?php echo _( "Once all of your Domain names have been deleted, you can continue with the next step here." ) ?>
                        <ul>
                            <li><?php echo _( "Remember that all certificates associated with those domains will be revoked." ) ?></li>
                        </ul>
                    </li>
                    <li><?php echo _( "Once all of your Domains are gone, file a Dispute for each E-Mail address, other than the primary one, that you have associated with your account." ) ?></li>
                    <li><?php echo _( "Finally, file a Dispute for your Primary E-Mail address for the account." ) ?></li>
                    <li><?php echo _( "Once everything connected to the old account has been deleted, you can re-add them to the new account and generate new certificates." ) ?></li>
                </ul>
            </li>
        </ul>
    </li>
    <li><?php echo _( "Ask an Assurer to assist with your Password recovery" ) ?>
        <ul>
            <li><?php echo _( "Meet with an Assurer in person ( face to face ) and follow the procedure in " ) ?><a
                        href="https://wiki.cacert.org/Support/PasswordRecoverywithAssurance"
                        title="Password Recovery with Assurance">Password Recovery with Assurance</a></li>
        </ul>
    </li>
    <li><?php echo _( "If NONE of the above are successful, there is one more option." ) ?>
        <ul>
            <li><?php echo _( "You can contact the Support Team and request that they assist you." ) ?>
                <ul>
                    <li><?php echo _( "The Support Team has few members and they often have many tasks outstanding, so this process can take a long time." ) ?></li>
                    <li><?php echo _( "We also require a payment for their time." ) ?></li>
                    <li><?php echo _( "There are several steps in this process." ) ?>
                        <ul>
                            <li><?php echo _( "Read through all of these steps before continuing.  You may need to return to this list several times." ) ?></li>
                            <li><?php echo _( "Click on the CAcert Payment button at the bottom of this page." ) ?></li>
                            <li><?php echo _( "This will take you to Paypal, and charge you the equivalent of USD 15, the value of approximately 1/2 hour of a Support Team member's professional time." ) ?></li>
                            <li><?php echo _( "Once that is completed send an e-mail message to support@cacert.org, with the Subject 'Password Recovery Request'. " ) ?></li>
                            <li><?php echo _( "In the body of that message, include the following:" ) ?>
                                <ul>
                                    <li><?php echo _( "Your Primary E-Mail address." ) ?></li>
                                    <li><?php echo _( "Which Password Recovery methods from the list above that you have tried and failed." ) ?></li>
                                    <li><?php echo _( "Any other information that might assist Support in helping you." ) ?></li>
                                    <li><?php echo _( "Do NOT include your Date of Birth in this e-mail message." ) ?></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
</ol>
<form method="post" action="index.php" autocomplete="off">
    <table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
        <tr>
            <td colspan="2" class="title"><?= _( "Lost Pass Phrase" ) ?></td>
        </tr>
        <tr>
            <td class="DataTD" width="125"><?= _( "Email Address (primary)" ) ?>:</td>
            <td class="DataTD" width="125"><input type="text" name="email" autocomplete="off"></td>
        </tr>
        <tr>
            <td class="DataTD"><?= _( "Date of Birth" ) ?><br>
                (<?= _( "dd/mm/yyyy" ) ?>)
            </td>
            <td class="DataTD">
                <nobr><select name="day">
						<?
						for ( $i = 1; $i <= 31; $i++ ) {
							echo "<option>$i</option>";
						}
						?>
                    </select>
                    <select name="month">
						<?
						for ( $i = 1; $i <= 12; $i++ ) {
							echo "<option value='$i'";
							echo ">" . ucwords( strftime( "%B", mktime( 0, 0, 0, $i, 1, date( "Y" ) ) ) ) . "</option>";
						}
						?>
                    </select>
                    <input type="text" name="year" size="4" autocomplete="off"></nobr>
            </td>
        </tr>
        <tr>
            <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?= _( "Next" ) ?>"></td>
        </tr>
    </table>
    <input type="hidden" name="oldid" value="<?= $id ?>">
</form>


<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="image" src="/images/payment2.png" border="0" name="submit"
           alt="<?= _( "Password Reset Payment through PayPal" ) ?>">
    <input type="hidden" name="encrypted"
           value="-----BEGIN PKCS7-----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-----END PKCS7-----">
</form>
