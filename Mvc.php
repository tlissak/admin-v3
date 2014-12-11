<?php


class Mvc{

    public function RenderPanel($id,$pnl){
        $tpl ='
                <div class="main-form col-md-6 {$pull}">
                    <div class="panel panel-default">
                        <div class="panel-heading clearfix">
                            <a class="btn btn-link pull-right"
                               href="javascript:void(0)"
                               data-toggle="collapse" data-target="#form-panel-{$id}"
                               aria-expanded="true" aria-controls="form-panel" ><i class="icon ion-ios7-arrow-up"></i></a>
                            <h3 class="panel-title"><i class="icon ion-{$icon}"></i> {$title} Form </h3>
                        </div>
                        <div class="panel-body collapse in" id="form-panel-{$id}">
                            {$cont}
                        </div>
                    </div>
                </div>
                ';
        $tpl = str_replace('{$id}',$id,$tpl) ;
        $tpl = str_replace('{$pull}',$pnl['pull'],$tpl) ;
        $tpl = str_replace('{$icon}',$pnl['icon'],$tpl) ;
        $tpl = str_replace('{$title}',$pnl['title'],$tpl) ;
        $tpl = str_replace('{$cont}',$pnl['cont'],$tpl) ;

        return $tpl;
    }

}

?>