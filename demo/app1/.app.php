<?php

class app1Model
{
    static function actionDefault( $ctrl, $data ) {
        echo "app1Model::Default, action=".$ctrl->currAct();
        $ctrl->nextModel( 'Err404' );
    }
    static function actionList( $ctrl, $data ) {
        //
        echo "Model::List action=".$ctrl->currAct();
    }
}

$ctrl->prependModel( 'app1Model', 'model' );

