<?php


class PanelMvc{

    public function RenderPanel($id,$cont,$type,$title,$icon,$control_btn=''){
        $tpl ='
                <div class="panel-compact panel-{$type}">
                    <div class="panel panel-default">
                        <div class="panel-heading" >

                            <a class="btn pull-right"
                               href="javascript:void(0)"
                               data-toggle="collapse" data-target="#form-panel-{$id}"
                               aria-expanded="true" aria-controls="form-panel" ><i class="icon ion-ios-arrow-up"></i></a>
                            {$control_btn}
                            <h3 class="panel-title" data-toggle="collapse" data-target="#form-panel-{$id}"
                               aria-expanded="true" aria-controls="form-panel"><i class="{$icon}"></i>
                                {$title}
                            </h3>


                        </div>
                        <div class="panel-body collapse in" id="form-panel-{$id}">
                            {$cont}
                        </div>
                    </div>
                </div>
                ';
        $tpl = str_replace('{$id}',$id,$tpl) ;
        $tpl = str_replace('{$type}',$type,$tpl) ;
        $tpl = str_replace('{$control_btn}',$control_btn,$tpl) ;
        $tpl = str_replace('{$icon}',$icon,$tpl) ;
        $tpl = str_replace('{$title}',$title,$tpl) ;
        $tpl = str_replace('{$cont}',$cont,$tpl) ;

        return $tpl;
    }

}

?>