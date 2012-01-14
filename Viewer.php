<?php
namespace CenaDta\App;

class Viewer
{
    function actionDefault( $ctrl, $data ) {
        // everything OK.
    }
    function actionErr404( $ctrl, $data ) {
        // show some excuses, or blame user for not finding a page.
        echo 'page not found...';
    }
    function actionException( $ctrl, $data ) {
        // show some nasty things happened and apologize.
        echo 'something terrible has happend...';
    }
}

