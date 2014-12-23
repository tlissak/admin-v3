<?php


class PanelMvc{

    public function RenderPanel($id,$cont,$type,$title,$icon){
        $tpl ='
                <div class="panel-compact">
                    <div class="panel panel-default">
                        <div class="panel-heading clearfix">
                            <a class="pull-right"
                               href="javascript:void(0)"
                               data-toggle="collapse" data-target="#form-panel-{$id}"
                               aria-expanded="true" aria-controls="form-panel" ><i class="icon ion-ios7-arrow-up"></i></a>
                            <h3 class="panel-title"><i class="{$icon}"></i> {$title} {$type} </h3>
                        </div>
                        <div class="panel-body collapse in" id="form-panel-{$id}">
                            {$cont}
                        </div>
                    </div>
                </div>
                ';
        $tpl = str_replace('{$id}',$id,$tpl) ;
        $tpl = str_replace('{$type}',$type,$tpl) ;
        //$tpl = str_replace('{$pull}',$pnl['pull'],$tpl) ;
        $tpl = str_replace('{$icon}',$icon,$tpl) ;
        $tpl = str_replace('{$title}',$title,$tpl) ;
        $tpl = str_replace('{$cont}',$cont,$tpl) ;

        return $tpl;
    }

}

?>