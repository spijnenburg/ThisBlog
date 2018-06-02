<?php

set_error_handler("errorMessage");

function errorMessage($errType, $errMessage, $errFile, $errLine) {
    ?>
        <div class="errorhandler">
            <strong>Foutmelding: </strong> Excuses, er ging iets mis op deze pagina. <br>
            <?php print "Foutcode: $errType - $errMessage op regel $errLine in $errFile"; ?>
        </div>
    <?php
}
