<?php
loadem("index");
$id = intval($id);
//showheader(_("CAcert - Non-Related Persons - Disclaimer and Licence"));
?>

<table border="1" bgcolor="#EEEEEE"><tr><td>

<h1 align="center"> <?=_("Non-Related Persons")?>  </h1>
<h2 align="center"> <?=_("(Disclaimer and Licence)")?> </h2>


<h2> <?=_("Definitions")?> </h2>

<p>
<?=_("This is a Disclaimer and Licence from
<u> CAcert Inc </u>,
the 'issuer',
to you, the 'user',
being a general user of the Internet.")?>
</p>

<h2> Disclaimer </h2>

<p>
<?=_("The issuer has no other agreement with you,
and has no control nor knowledge
as to how you intend to use the products of the issuer.
You alone take on all of the risk and all of the
liability of your usage.
The issuer makes no guarantee, warranty nor promise to you.")?>
</p>

<p>
<?=_("Therefore, to the fullest extent possible in law,
<b>ISSUER DISCLAIMS ALL LIABILITY TO YOU</b>
on behalf of itself and its related parties.")?>
</p>

<h2> <?=_("Licence")?> </h2>

<p>
<?=_("This licence offers you a non-exclusive, non-transferable
'PERMISSION TO USE' certificates issued by issuer.")?>
</p>

<ul><li>
    <?=_("You may 'USE' the certificates as facilitated
    by your software.  For example,
    you may construct connections, read emails,
    load code or otherwise, as facilitated by your
    software.")?>
  </li><li>
    <?=_("You may NOT RELY on any statements or claims
    made by the certificates or implied in any way.")?>
  </li><li>
    <?=_("If your software is licensed under a separate
    third party agreement, it may be permitted
    to make statements or claims based on the certificates.
    You may NOT RELY on these statements or claims.")?>
  </li><li>
    <?=_("You may NOT distribute certificates or root keys
    under this licence, nor make representation
    about them.")?>
</li></ul>

</td></tr></table>

<h2> <?=_("Alternatives")?> </h2>

<p>
<?=_("If you find the terms of the above
Non-Related Persons
Disclaimer and Licence
difficult or inadequate for your use, you may wish to")?>
</p>

<ul><li>
    <?=sprintf(_("As an individual,
        %sregister with issuer%s
    and enter into the user agreement.
    This is free."),"<a href='https://www.cacert.org/index.php?id=1'>","</a>")?>
  </li><li>
    <?=_("As a Third Party Distributor,
    enter into a separate third party agreement
    with issuer.")?>
  </li><li>
    <?=_("Delete issuer's roots from your software.
    Your software documentation should give
    directions and assistance for this.")?>
</li></ul>

<p>
<?=_("These alternatives are outside the above
Non-Related Persons Disclaimer and Licence
and do not incorporate.")?>
</p>


