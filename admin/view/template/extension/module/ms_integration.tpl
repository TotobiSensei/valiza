<?php echo $header; ?><?php echo $column_left;?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-ms_integration" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach($breadcrumbs as $breadcrumb): ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if($error_warning): ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php endif; ?>
        <?php if($success): ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php endif; ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-setting" data-toggle="tab"><?php echo $tab_setting ?></a></li>
                    <?php if($ms):  ?>
                    <li><a href="#tab-products" data-toggle="tab"><?php echo $tab_products ?></a></li>
                    <li><a href="#tab-orders" data-toggle="tab"><?php echo $tab_orders ?></a></li>
                    <li><a href="#tab-import" data-toggle="tab"><?php echo $tab_import ?></a></li>
                    <?php endif; ?>
                    <li><a href="#tab-info" data-toggle="tab">Информация</a></li>
                </ul>
                <form action="<?php echo $action ?>" method="post" enctype="multipart/form-data" id="form-ms_integration" class="form-horizontal">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-setting">
                            <?php if($checkTrial): ?>
                            <div class="panel panel-warning">
                                <div class="panel-heading" style="color: #f3a638; background-color: #fce7c8; border-color: #f9d5a2;"><h3 class="panel-title"><i class="fa fa-exclamation-triangle"></i> Вы используете триал версию модуля</h3></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="col-sm-12" style="font-weight: bold">
                                            Лицензия действительна до <?php echo $checkTrial ?>.
                                        </div>
                                        <div class="col-sm-12" style="font-weight: bold">
                                            Вы можете приобрести расщиренную версию модуля по <a href="http://ciframe.ru/moysklad-opencart-connector/" target="_blank">ссылке</a>.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if($check_licence!=''):  ?>
                            <div class="panel panel-danger">
                                <div class="panel-heading" style="background-color: #f5c1bb;border-color: #f3b5ad;"><h3 class="panel-title"><i class="fa fa-exclamation-triangle"></i> Ошибка</h3></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <h4><?php echo $check_licence ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="panel panel-default">
                                <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-link"></i> Основные параметры</h3></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_status" id="input-status" class="form-control">
                                                <?php if($settings["ms_integration_status"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_status ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ms-login"><?php echo $entry_ms_login ?></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="ms_integration_ms_login" id="input-ms-login" class="form-control" value="<?php echo $settings['ms_integration_ms_login'] ?>">
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_ms_login ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ms-password"><?php echo $entry_ms_password ?></label>
                                        <div class="col-sm-4">
                                            <input type="password" name="ms_integration_ms_password" id="input-ms-password" class="form-control" value="<?php echo $settings['ms_integration_ms_password'] ?>">
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_ms_password ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-license"><?php echo $entry_licence ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="ms_integration_licence" id="input-license" class="form-control" value="<?php echo $settings['ms_integration_licence'] ?>">
                                        </div>
                                    </div>
                                    <?php if($ms):  ?>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-cron-time"><?php echo $entry_cron_time ?></label>
                                        <div class="col-sm-4">
                                            <input type="number" name="ms_integration_cron_time" id="input-cron-time" class="form-control" value="<?php echo $settings['ms_integration_cron_time'] ?>">
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_cron_time ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php if($ms):  ?>
                        <div class="tab-pane" id="tab-products">
                            <div class="panel panel-default">
                                <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-link"></i> Настройки связки</h3></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ms-id"><?php echo $entry_ms_id ?></label>
                                        <div class="col-sm-4">
                                            <select  name="ms_integration_ms_id" id="input-ms-id" class="form-control">
                                                <?php foreach($setting_ms_select as $key=>$name):  ?>
                                                <?php if($key==$settings["ms_integration_ms_id"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_ms_id ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-oc-id"><?php echo $entry_oc_id ?></label>
                                        <div class="col-sm-4">
                                            <select  name="ms_integration_oc_id" id="input-oc-id" class="form-control">
                                                <?php foreach($setting_oc_select as $key=>$name):  ?>
                                                <?php if($key==$settings["ms_integration_oc_id"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_oc_id ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-language"><?php echo $entry_language ?></label>
                                        <div class="col-sm-4">
                                            <select  name="ms_integration_language" id="input-language" class="form-control">
                                                <?php foreach($setting_language_select as $key=>$name): ?>
                                                <?php if($key==$settings["ms_integration_language"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_language ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-checkbox_filter">Выбор фильтра по галочке</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_checkbox_filter" id="input-checkbox_filter" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_product_attributes_boolean as $key=>$name): ?>
                                                <?php if($key==$settings["ms_integration_checkbox_filter"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            В интеграции будут участвовать только товары у которых установлена выбранная галочка. При пустом значении будут участвовать все товары.
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Выбор отделов</label>
                                        <div class="col-sm-4">
                                            <div class="well well-sm" style="height: 150px; overflow: auto;">
                                                <?php foreach($setting_group_select as $key => $name):  ?>
                                                <div class="checkbox">
                                                    <label> <?php if(in_array($key,$settings["ms_integration_product_group"])): ?>
                                                        <input type="checkbox" name="ms_integration_product_group[]" value="<?php echo $key ?>" checked="checked" />
                                                        <?php echo $name ?>
                                                        <?php else: ?>
                                                        <input type="checkbox" name="ms_integration_product_group[]" value="<?php echo $key ?>" />
                                                        <?php echo $name ?>
                                                        <?php endif; ?></label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            Выбор отделов c которых будут выгружатся товары
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" style="cursor: pointer" data-toggle="collapse" data-target="#product-create-panel"><h3 class="panel-title"><i class="fa fa-plus"></i>Настройки создания товаров</h3></div>
                                <div id="product-create-panel" class="panel-body collapse in">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-create-product"><?php echo $entry_create_product ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_create_product" id="input-create-product" class="form-control">
                                                <?php if($settings["ms_integration_create_product"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_create_product ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-subtract_create"><?php echo $entry_subtract_create ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_subtract_create" id="input-subtract_create" class="form-control">
                                                <?php if($settings["ms_integration_subtract_create"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_subtract_create ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-shipping_create"><?php echo $entry_shipping_create ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_shipping_create" id="input-shipping_create" class="form-control">
                                                <?php if($settings["ms_integration_shipping_create"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_shipping_create ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-status_create"><?php echo $entry_status_create ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_status_create" id="input-status_create" class="form-control">
                                                <?php if($settings["ms_integration_status_create"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_status_create ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-stock_status_create"><?php echo $entry_stock_status_create ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_stock_status_create" id="input-stock_status_create" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_stock_status_select as $key => $name): ?>
                                                <?php if($key==$settings["ms_integration_stock_status_create"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_stock_status_create ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" style="cursor: pointer" data-toggle="collapse" data-target="#product-update-panel"><h3 class="panel-title"><i class="fa fa-repeat"></i> Настройки обновления товаров</h3></div>
                                <div id="product-update-panel" class="panel-body collapse in">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-stock"><?php echo $entry_stock ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_stock" id="input-stock" class="form-control">
                                                <?php if($settings["ms_integration_stock"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_stock ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-stock_out_status"><?php echo $entry_stock_out_status ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_stock_out_status" id="input-stock_out_status" class="form-control">
                                                <?php if($settings["ms_integration_stock_out_status"]==1): ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <option value="2">Оставлять без изменений</option>
                                                <?php else: ?>
                                                <?php if($settings["ms_integration_stock_out_status"]==0): ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <option value="2">Оставлять без изменений</option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <option value="2" selected="selected">Оставлять без изменений</option>
                                                <?php endif; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_stock_out_status ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-in_stock_status"><?php echo $entry_in_stock_status ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_in_stock_status" id="input-in_stock_status" class="form-control">
                                                <?php if($settings["ms_integration_in_stock_status"]==1): ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <option value="2">Оставлять без изменений</option>
                                                <?php else: ?>
                                                <?php if($settings["ms_integration_in_stock_status"]==0): ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <option value="2">Оставлять без изменений</option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <option value="2" selected="selected">Оставлять без изменений</option>
                                                <?php endif; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_in_stock_status ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo $entry_stock_store ?></label>
                                        <div class="col-sm-4">
                                            <div class="well well-sm" style="height: 150px; overflow: auto;">
                                                <?php foreach($setting_store_select as $key => $store):  ?>
                                                <div class="checkbox">
                                                    <label> <?php if(in_array($key,$settings["ms_integration_stock_store"])): ?>
                                                        <input type="checkbox" name="ms_integration_stock_store[]" value="<?php echo $key ?>" checked="checked" />
                                                        <?php echo $store ?>
                                                        <?php else: ?>
                                                        <input type="checkbox" name="ms_integration_stock_store[]" value="<?php echo $key ?>" />
                                                        <?php echo $store ?>
                                                        <?php endif; ?></label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_stock_store ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-stock_select">Значение для передачи остатка</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_stock_select" id="input-stock_select" class="form-control">
                                                <?php foreach($setting_stock_select as $key => $name): ?>
                                                <?php if($key==$settings["ms_integration_stock_select"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            Выбор значения для остатка из Мой Склад. Значение поля Доступно в Мой склад считается по формуле Остаток+Ожидание-Резерв
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-name_update"><?php echo $entry_name_update ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_name_update" id="input-name_update" class="form-control">
                                                <?php if($settings["ms_integration_name_update"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_name_update ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-description_update"><?php echo $entry_description_update ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_description_update" id="input-description_update" class="form-control">
                                                <?php if($settings["ms_integration_description_update"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_description_update ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ms_meta_title">Поле Meta Title</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_meta_title" id="input-ms_meta_title" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_product_text_attributes_select as $key=>$name):  ?>
                                                <?php if($key==$settings["ms_integration_meta_title"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            Источник для поля Meta Title
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ms_meta_description">Поле Meta Description</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_meta_description" id="input-ms_meta_description" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_product_text_attributes_select as $key=>$name):  ?>
                                                <?php if($key==$settings["ms_integration_meta_description"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            Источник для поля Meta Description
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ms_meta_keyword">Поле Meta Keyword</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_meta_keyword" id="input-ms_meta_keyword" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_product_text_attributes_select as $key=>$name):  ?>
                                                <?php if($key==$settings["ms_integration_meta_keyword"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            Источник для поля Meta Keyword
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-price_update"><?php echo $entry_price_update ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_price_update" id="input-price_update" class="form-control">
                                                <?php if($settings["ms_integration_price_update"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_price_update ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ms_price_type"><?php echo $entry_ms_price_type ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_ms_price_type" id="input-ms_price_type" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_price_select as $key=>$name):  ?>
                                                <?php if($key==$settings["ms_integration_ms_price_type"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_ms_price_type ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ms_special_prices">Акционные цены</label>
                                        <div class="col-sm-4">
                                            <div class="well well-sm" style="overflow: auto;">
                                                <?php foreach($setting_price_select_2 as $key => $name): ?>
                                                <div class="col-sm-4"> <?php echo $name ?></div>
                                                <div class="col-sm-8" style="padding-bottom: 10px">
                                                    <select name="ms_integration_special_prices[<?php echo $key ?>][customer_group_id]" class="form-control">
                                                        <option value=""></option>
                                                        <?php foreach($customer_groups as $customer_group): $selected = false; ?>
                                                        <?php foreach($settings["ms_integration_special_prices"] as $key_s => $value): ?>
                                                        <?php if($key == $key_s && $value["customer_group_id"] == $customer_group["customer_group_id"]): ?>
                                                        <option value="<?php echo $customer_group["customer_group_id"] ?>" selected="selected"><?php echo $customer_group["name"] ?></option>
                                                        <?php $selected=true; endif; ?>
                                                        <?php endforeach; ?>
                                                        <?php if(!$selected): ?>
                                                        <option value="<?php echo $customer_group["customer_group_id"] ?>"><?php echo $customer_group["name"] ?></option>
                                                        <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            Выберете какая группа покупателей соответствует ценам
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-weight_update"><?php echo $entry_weight_update ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_weight_update" id="input-weight_update" class="form-control">
                                                <?php if($settings["ms_integration_weight_update"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_weight_update ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ean_update"><?php echo $entry_ean_update ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_ean_update" id="input-ean_update" class="form-control">
                                                <?php if($settings["ms_integration_ean_update"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_ean_update ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-image_update"><?php echo $entry_image_update ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_image_update" id="input-image_update" class="form-control">
                                                <?php if($settings["ms_integration_image_update"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_image_update ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-multi_images">Обновление дополнительных фото</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_multi_images" id="input-multi_images" class="form-control">
                                                <?php if($settings["ms_integration_multi_images"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-image_names">Имена изображений</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_image_names" id="input-image_names" class="form-control">
                                                <?php if($settings["ms_integration_image_names"]):  ?>
                                                <option value="1" selected="selected">Оригинальные имена</option>
                                                <option value="0">Сгенерированные имена</option>
                                                <?php else: ?>
                                                <option value="1">Оригинальные имена</option>
                                                <option value="0" selected="selected">Сгенерированные имена</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            Сгенерированые имена - всегда уникальны, но не заменяют существующие ранее файлы изображений.<br>
                                            Оригинальные имена - используют только корень каталога изображений и при одинаковых именах могут перезаписать изображение одного товара другим.
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Выбор Атрибутов</label>
                                        <div class="col-sm-4">
                                            <div class="well well-sm" style="height: 150px; overflow: auto;">
                                                <?php foreach($setting_product_attributes_select as $key => $attribute):  ?>
                                                <div class="checkbox">
                                                    <label> <?php if(in_array($key,$settings["ms_integration_product_attributes"])): ?>
                                                        <input type="checkbox" name="ms_integration_product_attributes[]" value="<?php echo $key ?>" checked="checked" />
                                                        <?php echo $attribute ?>
                                                        <?php else: ?>
                                                        <input type="checkbox" name="ms_integration_product_attributes[]" value="<?php echo $key ?>" />
                                                        <?php echo $attribute ?>
                                                        <?php endif; ?></label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            Выберете какие Дополнительные поля с Мой Склад будут переданы в Атрибуты(Характеристики) товара на сайте
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-attribute_group">Группа аттрибутов</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_attribute_group" id="input-attribute_group" class="form-control">
                                                <option value="">По названию атрибута</option>
                                                <?php foreach($attribute_groups as $attribute_group): ?>
                                                <?php if($settings["ms_integration_attribute_group"]==$attribute_group["attribute_group_id"]): ?>
                                                <option value="<?php echo $attribute_group['attribute_group_id'] ?>" selected="selected"><?php echo $attribute_group["name"] ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $attribute_group['attribute_group_id'] ?>"><?php echo $attribute_group["name"] ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            Группа для новых аттрибутов
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-length">Длина</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_length" id="input-length" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_product_attributes_select as $key=>$name): ?>
                                                <?php if($key==$settings["ms_integration_length"]): ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            Выберете поле для загрузки длины с МС
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-width">Ширина</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_width" id="input-width" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_product_attributes_select as $key=>$name): ?>
                                                <?php if($key==$settings["ms_integration_width"]): ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            Выберете поле для загрузки ширины с МС
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-height">Высота</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_height" id="input-height" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_product_attributes_select as $key=>$name): ?>
                                                <?php if($key==$settings["ms_integration_height"]): ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            Выберете поле для загрузки высоты с МС
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-manufacturer">Производитель</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_manufacturer" id="input-manufacturer" class="form-control">
                                                <option value=""></option>
                                                <?php if ('supplier'==$settings["ms_integration_manufacturer"]): ?>
                                                <option value="supplier" selected="selected">Поставщик</option>
                                                <?php else: ?>
                                                <option value="supplier">Поставщик</option>
                                                <?php endif; ?>
                                                <?php foreach($setting_product_attributes_select as $key=>$name): ?>
                                                <?php if ($key==$settings["ms_integration_manufacturer"]): ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            Выберете поле для загрузки производителя с МС
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" style="cursor: pointer" data-toggle="collapse" data-target="#product-modification-panel"><h3 class="panel-title"><i class="fa fa-check-circle"></i> Настройки работы с модификациями/опциями</h3></div>
                                <div id="product-modification-panel" class="panel-body collapse in">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-modifications"><?php echo $entry_modifications ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_modifications" id="input-modifications" class="form-control">
                                                <?php if($settings["ms_integration_modifications"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_modifications ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-create_product_option"><?php echo $entry_create_product_option ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_create_product_option" id="input-create_product_option" class="form-control">
                                                <?php if($settings["ms_integration_create_product_option"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_create_product_option ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-create_option"><?php echo $entry_create_option ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_create_option" id="input-create_option" class="form-control">
                                                <?php if($settings["ms_integration_create_option"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_create_option ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" style="cursor: pointer" data-toggle="collapse" data-target="#product-category-panel"><h3 class="panel-title"><i class="fa fa-object-group"></i> Настройки работы с категориями товаров</h3></div>
                                <div id="product-category-panel" class="panel-body collapse in">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-categories"><?php echo $entry_categories ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_categories" id="input-categories" class="form-control">
                                                <?php if($settings["ms_integration_categories"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_categories ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-category_link"><?php echo $entry_category_link ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_category_link" id="input-category_link" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_category_link as $key => $name):  ?>
                                                <?php if($key==$settings["ms_integration_category_link"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_category_link ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-create_category"><?php echo $entry_create_category ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_create_category" id="input-create_category" class="form-control">
                                                <?php if($settings["ms_integration_create_category"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_create_category ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-category_name_update"><?php echo $entry_category_name_update ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_category_name_update" id="input-category_name_update" class="form-control">
                                                <?php if($settings["ms_integration_category_name_update"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_category_name_update ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-orders">
                            <div class="panel panel-default">
                                <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-link"></i> Основные параметры</h3></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ms-organization"><?php echo $entry_ms_organization ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_ms_organization" id="input-ms-organization" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_organization_select as $key=>$name):  ?>
                                                <?php if($key==$settings["ms_integration_ms_organization"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_ms_organization ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ms-agent"><?php echo $entry_ms_agent ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_ms_agent" id="input-ms-agent" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_agent_select as $key=>$name):  ?>
                                                <?php if($key==$settings["ms_integration_ms_agent"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_ms_agent ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-agent-search"><?php echo $entry_agent_search ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_agent_search" id="input-agent-search" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_agent_search_select as $key=>$name):  ?>
                                                <?php if($key==$settings["ms_integration_agent_search"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_agent_search ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading" style="cursor: pointer" data-toggle="collapse" data-target="#order-secondary-panel"><h3 class="panel-title"><i class="fa fa-link"></i> Дополнительные параметры</h3></div>
                                <div id="order-secondary-panel" class="panel-body collapse in">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-order_group">Выбор отдела</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_order_group" id="input-ms-order_group" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_group_select as $key => $name):  ?>
                                                <?php if($key==$settings["ms_integration_order_group"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            Выбор отдела к которому будет привязан заказ
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ms-store"><?php echo $entry_ms_store ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_ms_store" id="input-ms-store" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_store_select as $key => $name):  ?>
                                                <?php if($key==$settings["ms_integration_ms_store"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_ms_store ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ms-state"><?php echo $entry_ms_state ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_ms_state" id="input-ms-state" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_state_select as $key=>$name):  ?>
                                                <?php if($key==$settings["ms_integration_ms_state"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_ms_state ?>
                                            Синхронизация статусов работает только при выгрузке заказов по крону.
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-status_sync_type">В какую сторону синхронизировать статусы</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_status_sync_type" id="input-status_sync_type" class="form-control">
                                                <?php if($settings["ms_integration_status_sync_type"]=='2'):  ?>
                                                <option value="1">Из МойСклад в Магазин</option>
                                                <option value="2" selected="selected">Из Магазина в МойСклад</option>
                                                <?php else: ?>
                                                <option value="1" selected="selected">Из МойСклад в Магазин</option>
                                                <option value="2">Из Магазина в МойСклад</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            Для синхронизации с МойСклад в Магазин используйте отдельный крон. Синхронизация из магазина в МойСклад происходит с кроном обновления заказов
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Информация для обновления</label>
                                        <div class="col-sm-4">
                                            <div class="well well-sm" style="height: 150px; overflow: auto;">
                                                <?php foreach($setting_order_update_select as $key => $oupdate):  ?>
                                                <div class="checkbox">
                                                    <label> <?php if(in_array($key,$settings["ms_integration_order_update"])): ?>
                                                        <input type="checkbox" name="ms_integration_order_update[]" value="<?php echo $key ?>" checked="checked" />
                                                        <?php echo $oupdate ?>
                                                        <?php else: ?>
                                                        <input type="checkbox" name="ms_integration_order_update[]" value="<?php echo $key ?>" />
                                                        <?php echo $oupdate ?>
                                                        <?php endif; ?></label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            Выберете инфомацию для обновления в заказах Мой Склад.
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-name_mode">Формат имени контрагента</label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_name_mode" id="input-name_mode" class="form-control">
                                                <?php if($settings["ms_integration_name_mode"]):  ?>
                                                <option value="0">Имя Фамилия</option>
                                                <option value="1" selected="selected">Фамилия Имя</option>
                                                <?php else: ?>
                                                <option value="0" selected="selected">Имя Фамилия</option>
                                                <option value="1">Фамилия Имя</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-reserve"><?php echo $entry_reserve ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_reserve" id="input-reserve" class="form-control">
                                                <?php if($settings["ms_integration_reserve"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_reserve ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Статусы для резерва товаров</label>
                                        <div class="col-sm-4">
                                            <div class="well well-sm" style="height: 150px; overflow: auto;">
                                                <?php foreach($setting_state_select as $key => $name):  ?>
                                                <?php if($key!="sync"): ?>
                                                <div class="checkbox">
                                                    <label> <?php if(in_array($key,$settings["ms_integration_reserve_status"])): ?>
                                                        <input type="checkbox" name="ms_integration_reserve_status[]" value="<?php echo $key ?>" checked="checked" />
                                                        <?php echo $name ?>
                                                        <?php else: ?>
                                                        <input type="checkbox" name="ms_integration_reserve_status[]" value="<?php echo $key ?>" />
                                                        <?php echo $name ?>
                                                        <?php endif; ?></label>
                                                </div>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            При каких статусах резервировть товары в заказах
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-vatE"><?php echo $entry_ms_vatE ?></label>
                                        <div class="col-sm-4">
                                            <input type="number" name="ms_integration_ms_vatE" id="input-vatE" class="form-control" value="<?php echo $settings['ms_integration_ms_vatE'] ?>">
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_ms_vatE ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-vatI"><?php echo $entry_ms_vatI ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_ms_vatI" id="input-vatI" class="form-control">
                                                <?php if($settings["ms_integration_ms_vatI"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_ms_vatI ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-applicable"><?php echo $entry_ms_applicable ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_ms_applicable" id="input-applicable" class="form-control">
                                                <?php if($settings["ms_integration_ms_applicable"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_ms_applicable ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-order_name"><?php echo $entry_order_name ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_order_name" id="input-order_name" class="form-control">
                                                <?php if($settings["ms_integration_order_name"]):  ?>
                                                <option value="1" selected="selected"><?php echo "Использовать нумерацию Моего склада" ?></option>
                                                <option value="0"><?php echo "Использовать нумерацию магазина" ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo "Использовать нумерацию Моего склада" ?></option>
                                                <option value="0" selected="selected"><?php echo "Использовать нумерацию магазина" ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_order_name ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-order-prefix"><?php echo $entry_order_prefix ?></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="ms_integration_order_prefix" id="input-order-prefix" class="form-control" value="<?php echo $settings['ms_integration_order_prefix'] ?>">
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_order_prefix ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-order-suffix"><?php echo $entry_order_suffix ?></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="ms_integration_order_suffix" id="input-order-suffix" class="form-control" value="<?php echo $settings['ms_integration_order_suffix'] ?>">
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_order_suffix ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-ms_shipping"><?php echo $entry_ms_shipping ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_ms_shipping" id="input-ms_shipping" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_shipping_select as $key=>$name):  ?>
                                                <?php if($key==$settings["ms_integration_ms_shipping"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_ms_shipping ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading" style="cursor: pointer" data-toggle="collapse" data-target="#order-old-panel"><h3 class="panel-title"><i class="fa fa-link"></i> Параметры создания старых заказов</h3></div>
                                <div id="order-old-panel" class="panel-body collapse in">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-old_order_create"><?php echo $entry_old_order_create ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_old_order_create" id="input-old_order_create" class="form-control">
                                                <?php if($settings["ms_integration_old_order_create"]):  ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                                                <option value="0"><?php echo $text_disabled ?></option>
                                                <?php else: ?>
                                                <option value="1"><?php echo $text_enabled ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_old_order_create ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-order_link"><?php echo $entry_order_link ?></label>
                                        <div class="col-sm-4">
                                            <select name="ms_integration_order_link" id="input-order_link" class="form-control">
                                                <option value=""></option>
                                                <?php foreach($setting_order_link as $key=>$name): ?>
                                                <?php if($key==$settings["ms_integration_order_link"]):  ?>
                                                <option value="<?php echo $key ?>" selected="selected"><?php echo $name ?></option>
                                                <?php else: ?>
                                                <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                                <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php echo $help_order_link ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input-old_order_count">Количество заказов</label>
                                        <div class="col-sm-4">
                                            <div class="col-sm-4">
                                                <input type="number" name="ms_integration_old_order_count" id="input-old_order_count" class="form-control" value="<?php echo $settings['ms_integration_old_order_count'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            Количество последних заказов которые будут выгружены. Не более чем за последние три месяца.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-import">
                            <div class="form-group">
                                <h3 class="col-sm-12">
                                    <?php echo $help_save_setting ?>
                                </h3>
                            </div>
                            <?php if($integrationOn):  ?>
                            <div class="panel panel-default">
                                <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-link"></i> Работа с категориями</h3></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <a href="<?php echo $link_categories ?>"  style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit"><?php echo $category_text ?></a>
                                        </div>
                                        <div class="col-sm-9">
                                            <label class="control-label" style="text-align: left">Связать группы Моего склада с категрими магазина. Требуется для обновления категорий у товаров. <br>
                                                Функция не обновляет имена категорий. При включенном параметре "Создание категорий", создадутся недостающие.</label><br>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <a href="<?php echo $update_categories ?>" style="width: 100%" id="category-update-btn" class="btn btn-primary" type="submit">Обновить категоии</a>
                                        </div>
                                        <div class="col-sm-9">
                                            <label class="control-label" style="text-align: left">Обновление уже связанных категорий. Функция не создает недостающие категории.</label><br>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <a href="<?php echo $delete_categories ?>" style="width: 100%" id="category-delete-btn" class="btn btn-danger" onclick="if (confirm('Удалить?')) return true; else return false"><?php echo $delete_text ?></a>
                                        </div>
                                        <div class="col-sm-9">
                                            <label class="control-label" style="text-align: left">Удаление существующих связей категорий.</label><br>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <?php echo $category_count ?>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-link"></i> Работа с товарами</h3></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <a href="<?php echo $link_products ?>" style="width: 100%" id="product-link-btn" class="btn btn-primary" type="submit"><?php echo $import_text ?></a>
                                        </div>
                                        <div class="col-sm-9">
                                            <label class="control-label" style="text-align: left">Связать товары Моего склада с товарами магазина. Требуется для работы остальных функций модуля. <br>
                                                Функция не обновляет товары. При включенном параметре "Создание товаров", создадутся недостающие.</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <a href="<?php echo $update_products ?>" style="width: 100%" id="product-update-btn" class="btn btn-primary" type="submit">Обновить товары</a>
                                        </div>
                                        <div class="col-sm-9">
                                            <label class="control-label" style="text-align: left">Обновление уже связанных товаров. Функция не создает недостающие товары.</label><br>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <a href="<?php echo $update_stock ?>" style="width: 100%" id="product-stock-btn" class="btn btn-primary" type="submit">Обновить остатки</a>
                                        </div>
                                        <div class="col-sm-9">
                                            <label class="control-label" style="text-align: left">Остатки обновятся только у связанных товаров.</label><br>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <a href="<?php echo $delete_products ?>" style="width: 100%" id="product-delete-btn" class="btn btn-danger" onclick="if (confirm('Удалить?')) return true; else return false"><?php echo $delete_text ?></a>
                                        </div>
                                        <div class="col-sm-9">
                                            <label class="control-label" style="text-align: left">Удаление существующих связей товаров.</label><br>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <?php echo $products_count ?><br>
                                    <?php echo $bundles_count ?>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-link"></i> Работа с модификациями</h3></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <a href="<?php echo $link_variants ?>" style="width: 100%" id="variants-link-btn" class="btn btn-primary" type="submit"><?php echo $variant_text ?></a>
                                        </div>
                                        <div class="col-sm-9">
                                            <label class="control-label" style="text-align: left">Связать модификации Моего склада с опциями магазина. Свяжутся опции только у уже связанных товаров.<br>
                                                Функция не обновляет опции. При включенном параметров "Создание опций" и "Создание опций товаров", создадутся недостающие.</label><br>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <a href="<?php echo $update_variants ?>" style="width: 100%" id="variants-update-btn" class="btn btn-primary" type="submit">Обновить опции</a>
                                        </div>
                                        <div class="col-sm-9">
                                            <label class="control-label" style="text-align: left">Обновление уже связанных опций. Функция не создает недостающие опции.</label><br>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <a href="<?php echo $delete_variants ?>" style="width: 100%" id="variants-delete-btn" class="btn btn-danger" onclick="if (confirm('Удалить?')) return true; else return false"><?php echo $delete_text ?></a>
                                        </div>
                                        <div class="col-sm-9">
                                            <label class="control-label" style="text-align: left">Удаление существующих связей опций.</label><br>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <?php echo $modifications_count ?>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-link"></i> Работа с заказами</h3></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <a href="<?php echo $link_orders ?>" style="width: 100%" id="order-link-btn" class="btn btn-primary" type="submit"><?php echo $orders_text ?></a>
                                        </div>
                                        <div class="col-sm-9">
                                            <label class="control-label" style="text-align: left">Связать заказы магазина с заказами Моего склада. Связывает заказы только за последние 3 месяца<br>
                                                Функция не обновляет заказы. При включенном параметре "Создание старых заказов", создадутся недостающие.</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <a href="<?php echo $delete_orders ?>" style="width: 100%" id="order-delete-btn" class="btn btn-danger" onclick="if (confirm('Удалить?')) return true; else return false" ><?php echo $delete_text ?></a>
                                        </div>
                                        <div class="col-sm-9">
                                            <label class="control-label" style="text-align: left">Удаление существующих связей заказов.</label><br>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <?php echo $orders_count ?>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-link"></i> Работа с кронами</h3></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <h4><?php echo $help_cron ?></h4>
                                            <?php echo $cron_link ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <h4><?php echo $help_cron_2 ?></h4>
                                            <h4>Если заказы не выгружаются автоматически, настройте крон по номером 3</h4>
                                            <?php echo $cron_link_2 ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-3">
                                    <a href="<?php echo $link_repair_db ?>" style="width: 100%" id="repair-btn" class="btn btn-primary" onclick="if (confirm('Уверены?')) return true; else return false"><?php echo $repair_text ?></a>
                                </div>
                                <div class="col-sm-9">
                                    <label class="control-label" style="text-align: left"><?php echo $help_repair_db ?></label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-3">
                                    <a href="<?php echo $link_repair_events ?>" style="width: 100%" id="repair-btn-e" class="btn btn-primary" onclick="if (confirm('Уверены?')) return true; else return false">Исправить события</a>
                                </div>
                                <div class="col-sm-9">
                                    <label class="control-label" style="text-align: left">Пересоздание событий для создания заказов</label>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div class="tab-pane" id="tab-info">
                            <div class="form-group">
                                <div class="col-sm-6 col-md-3">
                                    <label>Видео по настройке модуля</label>
                                </div>
                                <div class="col-sm-6 col-md-9">
                                    <iframe width="560" height="315" src="https://www.youtube.com/embed/WPZo3CTAMIQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    Если у вас возникли проблемы с работой модуля, можете ознакомится с <a href='https://<?php echo $ciframeDomain ?>/faq/46/' target="_blank">ответами на часто задаваемые вопросы</a>. Если проблема не решена или вы не нашли нужный вам вопрос - обратится к нам в поддержку, написав на почту <a href="mailto:info@ciframe.com?subject=Вопросы по работе модуля">info@ciframe.com</a>.<br>
                                    Ознакомится с регламентом поддержки можете по <a href='https://<?php echo $ciframeDomain ?>/support/' target="_blank">ссылке</a>.
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <h4>Также можете ознакомится с другими нашими решения по <a href="https://<?php echo $ciframeDomain ?>/products/opencart/" target="_blank">OpenCart</a>, <a href="https://<?php echo $ciframeDomain ?>/products/moysklad/" target="_blank">Мой Склад</a> и <a href="https://<?php echo $ciframeDomain ?>/ecommerce-integration/" target="_blank">другим продуктам</a>.</h4>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-3">
                                    <a href="<?php echo $download_log ?>" style="width: 100%" id="download-btn" class="btn btn-primary">Скачать лог файл</a>
                                </div>
                                <div class="col-sm-9">
                                    <label class="control-label" style="text-align: left"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <h4 class="col-sm-12" style="text-align: center">
                    <?php echo $version ?>
                </h4>
            </div>
        </div>
    </div>
</div>
<?php echo $footer ?>