<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">

      <div class="pull-right">
        <button type="submit" form="form-module" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>

      <h1><?php echo $heading_title; ?></h1>

      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>

    </div>
  </div>

  <div class="container-fluid">
    <div id="settings-not-save" style="display:none">
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Настройки не сохранены!
      </div>
    </div>

    <?php if ($error_warning) { ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    <?php } ?>

    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>

    <div class="panel panel-default">
      <div class="panel-body">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1" data-toggle="tab">Синхронизация Мой Склад &#8658; Opencart</a></li>
        <li><a href="#tab-2" data-toggle="tab">Синхронизация Opencart &#8658; Мой Склад</a></li>
        <li><a href="#tab-3" data-toggle="tab">Настройки</a></li>
        <li><a href="#tab-4" data-toggle="tab">Лог</a></li>
      </ul>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-module" class="form-horizontal">
        <div class="tab-content">

          <div class="tab-pane active" id="tab-1">

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Синхронизация категорий</h3>
              </div>
              <div class="panel-body">

                <div class="form-group">
                  <label class="col-sm-3 control-label" style="text-align: left;" >Синхронизировать категории с порядковыми номерами: </label>
                  <div class="col-sm-3">
                    <select name="syncms_cat_offset" id="input-cat-offset" class="form-control">
                        <?php foreach ($cat_offset as $key => $value) { ?>
                          <?php if ($key != $cat_offset_key) { ?>
                            <option value="<?php echo $key ?>" ><?php echo $value ?></option>
                          <?php  } else { ?>
                            <option value="<?php echo $key ?>" selected="selected"><?php echo $value ?></option>
                          <?php } ?>
                        <?php } ?>
                    </select>
                  </div>
                  <div class="col-sm-5 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    После изменения нужно сохранить настройки!
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $category_add_href ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit" form = "form-module">Добавить категории</a>
                  </div>
                  <div class="col-sm-8 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Добавление категорий из Мой Склад на сайт. Наименования категорий должны быть уникальными! 
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $category_update_href ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit">Обновить категории</a>
                  </div>
                  <div class="col-sm-8 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Обновление добавленных на сайт категорий. У категорий обновляются категории-родители и мета-теги. Наименования категорий должны быть уникальными!
                  </div>
                </div>

              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Синхронизация товаров</h3>
              </div>
              <div class="panel-body">

                <div class="form-group">
                  <label class="col-sm-3 control-label" style="text-align: left;" >Синхронизировать товары с порядковыми номерами: </label>
                  <div class="col-sm-3">
                    <select name="syncms_product_offset" id="input-product-offset" class="form-control">
                      <?php foreach ($product_offset as $key => $value) { ?>
                        <?php if ($key != $product_offset_key) { ?>
                          <option value="<?php echo $key ?>" ><?php echo $value ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $key ?>" selected="selected"><?php echo $value ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-sm-5 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    После изменения нужно сохранить настройки!
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label" style="text-align: left;" >Связывать товары по:</label>
                  <div class="col-sm-3">
                    <select name="syncms_binding" id="input-binding" class="form-control">
                      <?php if ($binding == 'model_article') { ?>
                        <option value="model_code">Модель - Код</option>
                        <option value="model_externalCode">Модель - Внешний код</option>
                        <option value="model_article" selected="selected">Модель - Артикул</option>
                        <option value="sku_article">SKU - Артикул</option>
                        <option value="sku_code">SKU - Код</option>
                      <?php } else if ($binding == 'model_externalCode') { ?>
                        <option value="model_code">Модель - Код</option>
                        <option value="model_externalCode" selected="selected">Модель - Внешний код</option>
                        <option value="model_article">Модель - Артикул</option>
                        <option value="sku_article">SKU - Артикул</option>
                        <option value="sku_code">SKU - Код</option>
                      <?php } else if ($binding == 'sku_article') { ?>
                        <option value="model_code">Модель - Код</option>
                        <option value="model_externalCode">Модель - Внешний код</option>
                        <option value="model_article">Модель - Артикул</option>
                        <option value="sku_article" selected="selected">SKU - Артикул</option>
                        <option value="sku_code">SKU - Код</option>
                      <?php } else if ($binding == 'sku_code') { ?>
                        <option value="model_code">Модель - Код</option>
                        <option value="model_externalCode">Модель - Внешний код</option>
                        <option value="model_article">Модель - Артикул</option>
                        <option value="sku_article">SKU - Артикул</option>
                        <option value="sku_code" selected="selected">SKU - Код</option>
                      <?php } else { ?>
                        <option value="model_code" selected="selected">Модель - Код</option>
                        <option value="model_externalCode">Модель - Внешний код</option>
                        <option value="model_article">Модель - Артикул</option>
                        <option value="sku_article">SKU - Артикул</option>
                        <option value="sku_code">SKU - Код</option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $product_add_href ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit">Добавить товары</a>
                  </div>
                  <div class="col-sm-8 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Добавление товаров из Мой Склад на сайт. В Моем Складе не должно быть товаров-дубликатов, у которых совпадают: и наименование и поле, по которому происходит связка (код, артикул, внешний код).
                  </div>
                </div>  
                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $product_update_href ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit">Обновить товары</a>
                  </div>
                  <div class="col-sm-8 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Обновление добавленных на сайт товаров. При нажатии происходит обновление полей выбранных в настройках.
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $stock_update_href ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit">Обновить остатки</a>
                  </div>
                  <div class="col-sm-8 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Обновление остатков у добавленных на сайт товаров.
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $price_update_href ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit">Обновить цены</a>
                  </div>
                  <div class="col-sm-8 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Обновление цен у добавленных на сайт товаров.
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $absence_product_href ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit">Удалить/обновить<br>лишние товары</a>
                  </div>
                  <div class="col-sm-8 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Удаление/обнуление остатков/обновление статуса у товаров, которые есть на сайте, но отсутствуют в Моем Складе.
                  </div>
                </div>
              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Синхронизация изображений</h3>
              </div>
              <div class="panel-body">

                <div class="form-group">
                  <label class="col-sm-3 control-label" style="text-align: left;" >Синхронизировать товары с порядковыми номерами: </label>
                  <div class="col-sm-3">
                    <select name="syncms_image_offset" id="input-image-offset" class="form-control">
                      <?php foreach ($product_offset as $key => $value) { ?>
                        <?php if ($key != $image_offset_key) { ?>
                          <option value="<?php echo $key ?>" ><?php echo $value ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $key ?>" selected="selected"><?php echo $value ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-sm-5 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    После изменения нужно сохранить настройки!
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $image_sync_href ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit">Добавить/обновить изображения<br>товаров</a>
                  </div>
                  <div class="col-sm-8 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Добавление/обновление изображений у добавленных на сайт товаров. Изображения скачиваются и хранятся в директории /image/catalog/demo/syncms. В Моем Складе не должно быть несколько изображений с одинаковыми названиями и размерами!
                  </div>
                </div>

              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Синхронизация модификаций</h3>
              </div>
              <div class="panel-body">

                <div class="form-group">
                  <label class="col-sm-3 control-label" style="text-align: left;" >Синхронизировать модификации с порядковыми номерами: </label>
                  <div class="col-sm-3">
                    <select name="syncms_attr_offset" id="input-attr-offset" class="form-control">
                      <?php foreach ($attr_offset as $key => $value) { ?>
                        <?php if ($key != $attr_offset_key) { ?>
                          <option value="<?php echo $key ?>" ><?php echo $value ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $key ?>" selected="selected"><?php echo $value ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-sm-5 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    После изменения нужно сохранить настройки!
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $modification_sync_href ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit" >Добавить/обновить атрибуты и опции<br>товаров</a>
                  </div>
                  <div class="col-sm-8 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Добавление/обновление атрибутов и опций (модификаций) у добавленных на сайт товаров. Атрибуты будут добавляться в случае, когда у модификации в Моем Складе в описании написано "Атрибут" (без кавычек). В остальных случаях модификация будет добавляться как опция.
                  </div>
                </div>
              </div>
            </div>       

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Cron (Планировщик заданий)</h3>
              </div>
              <div class="panel-body">

                <div class="form-group">
                  <div class="col-sm-12">
                    <label class="control-label" style="text-align: left;">Планировщик Cron позволяет выполнять все вышеперечисленные действия автоматически в определенное время. В случае использования Cron синхронизироваться будут все товары и категории, а не только 1000. Ниже перечислены команды для каждого действия, которые необходимо внести в Cron:<br>
                    <br>Добавление категорий: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_category_add ?></i>
                    <br>Обновление категорий: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_category_update ?></i>

                    <br>Добавление товаров: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_product_add ?></i>
                    <br>Обновление товаров: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_product_update ?></i>
                    <br>Обновление отстаков: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_stock_update ?></i>
                    <br>Обновление цен: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_price_update ?></i>
                    <br>Удаление/обновление лишних товаров: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_sync_absence_products ?></i>
                    <br>Добавление/обновление изображение: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_image_sync ?></i>
                    <br>Добавление/обновление атрибутов и опций: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_modification_sync ?></i>
                    </label>
                  </div>
                </div>

              </div>
            </div>

          </div>
          <div class="tab-pane" id="tab-2">

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Синхронизация заказов</h3>
              </div>
              <div class="panel-body">

                <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Организация</label>
                <div class="col-sm-3">
                  <select name="syncms_organization" id="input-sale-price" class="form-control">
                    <?php foreach ($organization as $key => $value) { ?>
                      <?php if ($key != $organization_key) { ?>
                        <option value="<?php echo $key ?>" ><?php echo $value ?></option>
                      <?php } else { ?>
                        <option value="<?php echo $key ?>" selected="selected"><?php echo $value ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-sm-6 alert alert-info">
                  <i class="fa fa-info-circle"></i>
                  Организация, которая будет указана в заказах.
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Склад</label>
                <div class="col-sm-3">
                  <select name="syncms_store" id="input-sale-price" class="form-control">
                    <?php foreach ($store as $key => $value) { ?>
                      <?php if ($key != $store_key) { ?>
                        <option value="<?php echo $key ?>" ><?php echo $value ?></option>
                      <?php } else { ?>
                        <option value="<?php echo $key ?>" selected="selected"><?php echo $value ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-sm-6 alert alert-info">
                  <i class="fa fa-info-circle"></i>
                  Склад, который будет указан в заказах.
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Префикс заказов</label>
                <div class="col-sm-2">
                    <input name="syncms_order_prefix" type="text" id="order-prefix" class="form-control" value="<?php echo $order_prefix?>">
                </div>
                <div class="col-sm-7 alert alert-info">
                  <i class="fa fa-info-circle"></i>
                  Вы можете дополнить номер заказа, который будет передан в номер заказа/комментарий к заказу в Моем Складе префиксом. Уникальный префикс ускоряет синхронизацию заказов.
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Связывать заказы по:</label>
                <div class="col-sm-3">
                  <select name="syncms_order_binding" id="input-binding" class="form-control">
                    <?php if ($order_binding == 'number') { ?>
                      <option value="number" selected="selected">№ заказа ОС - № заказа МС</option>
                      <option value="comment">№ заказа ОС - Комментарий МС</option>
                    <?php } else { ?>
                      <option value="number">№ заказа ОС - № заказа МС</option>
                      <option value="comment" selected="selected">№ заказа ОС - Комментарий МС</option>
                    <?php } ?>
                  </select>
                </div>
              </div>

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $order_add_href ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit" >Добавить заказы <br>покупателей</a>
                  </div>
                  <div class="col-sm-8 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Добавление заказов из Опенкарта в Мой Склад. Если хотя-бы один товар в заказе из Opencart отсутствует в Моем Складе, то заказ в Мой Склад добавлен не будет!
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-3">
                    <a href="<?php echo $order_update_href ?>" style="width: 100%" id="category-link-btn" class="btn btn-primary" type="submit" >Обновить заказы <br>покупателей</a>
                  </div>
                  <div class="col-sm-8 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Обновление статусов заказов в Моем Складе.
                  </div>
                </div>

              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Cron (Планировщик заданий)</h3>
              </div>
              <div class="panel-body">

                <div class="form-group">
                  <div class="col-sm-12">
                    <label class="control-label" style="text-align: left;">Планировщик Cron позволяет выполнять все вышеперечисленные действия автоматически в определенное время. Ниже перечислены команды для каждого действия, которые необходимо внести в Cron:<br>
                    <br>Добавление заказов: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_order_add ?></i>
                    <br>Обновление заказов: &nbsp;&nbsp;
                    <i style="font-style: italic; font-weight: normal; font-size: 14px"><?php echo $cron_order_update ?></i>
                    </label>
                  </div>
                </div>

              </div>
            </div>

          </div>
          <div class="tab-pane" id="tab-3">

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Основные настройки</h3>
              </div>
              <div class="panel-body">

                <input name="syncms_admin_url" type="text" id = "login" class="form-control hide" value="<?php echo $admin_url ?>">
                
                <div class="form-group">
                  <label class="col-sm-1 control-label" style="text-align: left;" ><?php echo $entry_status ?></label>
                  <div class="col-sm-2">    
                    <select name="syncms_status" id="input-status" class="form-control">
                      <?php if ($status) { ?>
                      <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                      <option value="0"><?php echo $text_disabled ?></option>
                      <?php } else { ?>
                      <option value="1"><?php echo $text_enabled ?></option>
                      <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-2 control-label" style="text-align: left;" >Логин в Моем Складе</label>
                  <div class="col-sm-3">
                    <input name="syncms_login" type="text" id = "login" class="form-control" value="<?php echo $login ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-2 control-label" style="text-align: left;" >Пароль в Моем Складе</label>
                  <div class="col-sm-3">
                    <input name="syncms_password" type="password" id = "password" class="form-control" value="<?php echo $password ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-2 control-label" style="text-align: left;" >Токен из Моего Склада</label>
                  <div class="col-sm-3">
                    <input name="syncms_token" type="text" id = "password" class="form-control" value="<?php echo $token ?>">
                  </div>
                  <div class="col-sm-6 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Вместо логина и пароля можно использовать токен доступа к JSON API (можно создать в настройках Моего Склада).
                  </div>
                </div>

              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Добавление/обновление товаров</h3>
              </div>
            <div class="panel-body">
              
              <div class="form-group">
                <label class="col-sm-3 control-label" style="text-align: left;" >Дополнительно связывать по наименованию:</label>

                <div class="col-sm-2">
                  <?php if ($binding_name == 1) { ?>
                    <label class="radio-inline">
                      <input type="radio" name="syncms_binding_name" value="1" checked="checked"> Да
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="syncms_binding_name" value="0"> Нет
                    </label>
                  <?php } else { ?>
                    <label class="radio-inline">
                      <input type="radio" name="syncms_binding_name" value="1"> Да
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="syncms_binding_name" value="0" checked="checked"> Нет
                    </label>
                  <?php } ?>
                </div>
                <div class="col-sm-6 alert alert-info">
                  <i class="fa fa-info-circle"></i>
                  Необходимо включить если выбранный параметр связки товаров (код/артикул/внешний код) не является уникальным в Моем Складе! При включении дополнительной связки у товаров не будет обновляться наименование и URL, вместо этого будут создаваться новые товары!
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Цена продажи</label>
                <div class="col-sm-3">
                  <select name="syncms_sale_price" id="input-sale-price" class="form-control">
                    <?php foreach ($sale_prices as $key => $value) { ?>
                      <?php if ($key != $sale_price_key) { ?>
                        <option value="<?php echo $key ?>" ><?php echo $value ?></option>
                      <?php } else { ?>
                        <option value="<?php echo $key ?>" selected="selected"><?php echo $value ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                  <label class="col-sm-2 control-label" style="text-align: left;">Обновлять следующие поля товаров:</label>
                  <div class="col-sm-4">
                    <div class="well well-sm" style="height: 260px; overflow: auto;">
                        <div class="checkbox">
                          <?php if ($name_update == 'on') { ?>
                            <label>
                              <input type="checkbox" name="syncms_name_update" checked="checked"> Наименование
                            </label>
                          <?php } else { ?>
                            <label>
                              <input type="checkbox" name="syncms_name_update"> Наименование
                            </label>
                          <?php } ?>
                        </div>
                        <div class="checkbox">
                          <?php if ($desc_update == 'on') { ?>
                            <label>
                              <input type="checkbox" name="syncms_desc_update" checked="checked"> Описание
                            </label>
                          <?php } else { ?>
                            <label>
                              <input type="checkbox" name="syncms_desc_update"> Описание
                            </label>
                          <?php } ?>
                        </div>
                        <div class="checkbox">
                          <?php if ($url_update == 'on') { ?>
                            <label>
                              <input type="checkbox" name="syncms_url_update" checked="checked"> URL
                            </label>
                          <?php } else { ?>
                            <label>
                              <input type="checkbox" name="syncms_url_update"> URL
                            </label>
                          <?php } ?>
                        </div>
                        <div class="checkbox">
                          <?php if ($cat_update == 'on') { ?>
                            <label>
                              <input type="checkbox" name="syncms_cat_update" checked="checked"> Категория
                            </label>
                          <?php } else { ?>
                            <label>
                              <input type="checkbox" name="syncms_cat_update"> Категория
                            </label>
                          <?php } ?>
                        </div>
                        <div class="checkbox">
                          <?php if ($sku_update == 'on') { ?>
                            <label>
                              <input type="checkbox" name="syncms_sku_update" checked="checked"> Артикул (SKU)
                            </label>
                          <?php } else { ?>
                            <label>
                              <input type="checkbox" name="syncms_sku_update"> Артикул (SKU)
                            </label>
                          <?php } ?>
                        </div>
                        <div class="checkbox">
                          <?php if ($weight_update == 'on') { ?>
                            <label>
                              <input type="checkbox" name="syncms_weight_update" checked="checked"> Вес
                            </label>
                          <?php } else { ?>
                            <label>
                              <input type="checkbox" name="syncms_weight_update"> Вес
                            </label>
                          <?php } ?>
                        </div>
                        <div class="checkbox">
                          <?php if ($manufacturer_update == 'on') { ?>
                            <label>
                              <input type="checkbox" name="syncms_manufacturer_update" checked="checked"> Производитель (нужно доп. поле в МС)
                            </label>
                          <?php } else { ?>
                            <label>
                              <input type="checkbox" name="syncms_manufacturer_update"> Производитель (нужно доп. поле в МС)
                            </label>
                          <?php } ?>
                        </div>
                        <div class="checkbox">
                          <?php if ($stock_status_update == 'on') { ?>
                            <label>
                              <input type="checkbox" name="syncms_stock_status_update" checked="checked"> Статус при отсутствии на складе
                            </label>
                          <?php } else { ?>
                            <label>
                              <input type="checkbox" name="syncms_stock_status_update"> Статус при отсутствии на складе
                            </label>
                          <?php } ?> 
                        </div>
                     </div>
                  </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Единица измерения веса в Моем Складе</label>
                <div class="col-sm-3">
                  <select name="syncms_weight_unity" id="input-weight-unity" class="form-control">
                      <?php if ($weight_unity == 1) { ?>
                        <option value="1" selected="selected">Килограмм</option>
                        <option value="2">Грамм</option>
                      <?php } else { ?>
                        <option value="1">Килограмм</option>
                        <option value="2" selected="selected">Грамм</option>
                      <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Название доп. поля с производителями</label>
                <div class="col-sm-3">
                    <input placeholder="Производитель" name="syncms_manufacturer" type="text" id = "login" class="form-control" value="<?php echo $manufacturer?>">
                </div>
                <div class="col-sm-6 alert alert-info">
                  <i class="fa fa-info-circle"></i>
                    В это поле нужно вписать название (без кавычек) дополнительного поля с типом "Текст" в Моем Складе, в которое занесены производители товара.
                </div>
              </div>

              <div class="form-group">
                  <label class="col-sm-2 control-label" style="text-align: left;">Выгружать остатки из:</label>
                  <div class="col-sm-4">
                    <div class="well well-sm" style="height: 150px; overflow: auto;">
                      <?php foreach ($stock_store as $key => $value) { ?>
                        <div class="checkbox">
                        <?php if (is_array($stock_store_key) && in_array($key, $stock_store_key)) { ?>
                          <label>
                            <input type="checkbox" name="syncms_stock_store[]" checked="checked" value="<?php echo $key ?>"> <?php echo $value ?>
                          </label>
                        <?php } else { ?>
                          <label>
                            <input type="checkbox" name="syncms_stock_store[]" value="<?php echo $key ?>"> <?php echo $value ?>
                          </label>
                        <?php } ?>
                        </div>
                      <?php } ?>

                     </div>
                  </div>
                  <div class="col-sm-5 alert alert-info">
                    <i class="fa fa-info-circle"></i>
                      Если ни один склад не выбран, то остатки будут выгружаться из всех складов
                  </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Статус товаров при отсутствии на складе:</label>
                <div class="col-sm-3">
                  <select name="syncms_stock_status" id="input-stock-status" class="form-control">
                    <?php foreach ($stock_status as $key => $value) { ?>
                      <?php if ($key != $stock_status_key) { ?>
                        <option value="<?php echo $key ?>" ><?php echo $value ?></option>
                      <?php } else { ?>
                        <option value="<?php echo $key ?>" selected="selected"><?php echo $value ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label" style="text-align: left;" >Синхронизировать товары и категории только из группы и ее подгрупп:</label>
                <div class="col-sm-3">
                  <input name="syncms_from_group" type="text" placeholder="Название группы" class="form-control" value="<?php echo $from_group ?>">
                </div>
                <div class="col-sm-5 alert alert-info">
                  <i class="fa fa-info-circle"></i>
                    Оставьте это поле пустым, чтобы синхронизировать товары и категории со всех групп.
                </div>
              </div>

            </div>
          </div>

          <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Синронизация опций</h3>
              </div>
            <div class="panel-body">
              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Присваивать товарам суммы остатков их опций:</label>

                  <div class="col-sm-3">
                    <?php if ($sum_option == 1) { ?>
                      <label class="radio-inline">
                        <input type="radio" name="syncms_sum_option" value="1" checked="checked"> Да
                      </label>
                      <label class="radio-inline">
                        <input type="radio" name="syncms_sum_option" value="0"> Нет
                      </label>
                    <?php } else { ?>
                      <label class="radio-inline">
                        <input type="radio" name="syncms_sum_option" value="1"> Да
                      </label>
                      <label class="radio-inline">
                        <input type="radio" name="syncms_sum_option" value="0" checked="checked"> Нет
                      </label>
                    <?php } ?>   
                  </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label" style="text-align: left;" >Ставить опциям нулевую цену, если цена опций равна цене товара (не задана):</label>

                  <div class="col-sm-3">
                    <?php if ($zero_option_price == 1) { ?>
                      <label class="radio-inline">
                        <input type="radio" name="syncms_zero_option_price" value="1" checked="checked"> Да
                      </label>
                      <label class="radio-inline">
                        <input type="radio" name="syncms_zero_option_price" value="0"> Нет
                      </label>
                    <?php } else { ?>
                      <label class="radio-inline">
                        <input type="radio" name="syncms_zero_option_price" value="1"> Да
                      </label>
                      <label class="radio-inline">
                        <input type="radio" name="syncms_zero_option_price" value="0" checked="checked"> Нет
                      </label>
                    <?php } ?>   
                  </div>
              </div>

            </div> 
            
          </div>

          <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Meta-теги товаров</h3>
              </div>
            <div class="panel-body">
            <div class="form-group">
            <div class="col-sm-11 alert alert-info">
              <i class="fa fa-info-circle"></i>
                Доступные переменные: [name] - наименование товара, [price] - цена товара
            </div>
              <label class="col-sm-2 control-label" style="text-align: left;" >Обновлять мета-теги товаров:</label>

                <div class="col-sm-3">
                  <?php if ($meta_prod_update == 1) { ?>
                    <label class="radio-inline">
                      <input type="radio" name="syncms_meta_prod_update" value="1" checked="checked"> Да
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="syncms_meta_prod_update" value="0"> Нет
                    </label>
                  <?php } else { ?>
                    <label class="radio-inline">
                      <input type="radio" name="syncms_meta_prod_update" value="1"> Да
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="syncms_meta_prod_update" value="0" checked="checked"> Нет
                    </label>
                  <?php } ?>   
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Meta-тег title:</label>
                <div class="col-sm-4">
                  <textarea placeholder="Купить [name] по цене [price] р." class="form-control" name="syncms_prod_meta_title" rows="2"><?php echo $prod_meta_title?></textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Meta-тег description:</label>
                <div class="col-sm-4">
                  <textarea placeholder="Купить [name] по цене [price] р." class="form-control" name="syncms_prod_meta_desc" rows="3"><?php echo $prod_meta_desc?></textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Meta-тег keywords:</label>
                <div class="col-sm-4">
                 <textarea placeholder="купить [name] по цене [price] р." class="form-control" name="syncms_prod_meta_keyword" rows="2"><?php echo $prod_meta_keyword?></textarea>
                </div>
              </div>
            </div>
          </div>

          <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Meta-теги категорий</h3>
              </div>
            <div class="panel-body">
              <div class="form-group">
                <div class="col-sm-11 alert alert-info">
                  <i class="fa fa-info-circle"></i>
                    Доступные переменные: [name] - наименование категории
                </div>
                <label class="col-sm-2 control-label" style="text-align: left;" >Обновлять мета-теги категорий:</label>

                <div class="col-sm-3">
                  <?php if ($meta_cat_update == 1) { ?>
                    <label class="radio-inline">
                      <input type="radio" name="syncms_meta_cat_update" value="1" checked="checked"> Да
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="syncms_meta_cat_update" value="0"> Нет
                    </label>
                  <?php } else { ?>
                    <label class="radio-inline">
                      <input type="radio" name="syncms_meta_cat_update" value="1"> Да
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="syncms_meta_cat_update" value="0" checked="checked"> Нет
                    </label>
                  <?php } ?>   
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Meta-тег title:</label>
                <div class="col-sm-4">
                  <textarea placeholder="Купить [name]" class="form-control" name="syncms_cat_meta_title" rows="2"><?php echo $cat_meta_title?></textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Meta-тег description:</label>
                <div class="col-sm-4">
                  <textarea placeholder="Купить [name]" class="form-control" name="syncms_cat_meta_desc" rows="3"><?php echo $cat_meta_desc?></textarea>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" style="text-align: left;" >Meta-тег keywords:</label>
                <div class="col-sm-4">
                  <textarea placeholder="Купить [name]" class="form-control" name="syncms_cat_meta_keyword" rows="2"><?php echo $cat_meta_keyword?></textarea>
                </div>
              </div>
            </div>
          </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Лишние товары</h3>
              </div>
              <div class="panel-body">
                <div class="form-group">
                  <label class="col-sm-3 control-label" style="text-align: left;" >Что делать с отсутствующими в Моем Складе товарами:</label>
                  <div class="col-sm-3">
                    <select name="syncms_absence_products" id="input-absence-products" class="form-control">
                      <?php if ($absence_products == 0) { ?>
                        <option value="0" selected="selected">Удалять</option>
                        <option value="1">Обнулять остатки</option>
                        <option value="2">Обнулять остатки и обновлять статус</option>
                      <?php } else if ($absence_products == 1) { ?>
                        <option value="0">Удалять</option>
                        <option value="1" selected="selected">Обнулять остатки</option>
                        <option value="2">Обнулять остатки и обновлять статус</option>
                      <?php } else { ?>
                        <option value="0">Удалять</option>
                        <option value="1">Обнулять остатки</option>
                        <option value="2" selected="selected">Обнулять остатки и обновлять статус</option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-sm-6 alert alert-info">
                  <i class="fa fa-info-circle"></i>
                  "Обновлять статус товара" означает менять статусы товаров на "Нет в наличии" вместе с обнулением остатков товаров.
                </div>
                </div>
              </div>
            </div>
            </div>
            
            <div class="tab-pane" id="tab-4">
              <p><textarea readonly class="form-control" name="" id="" rows="20"><?php echo $log ?></textarea></p>
              
              <a href="<?php echo $log_clear_href ?>" class="btn btn-danger"><i class="fa fa-eraser"></i> Очистить</a>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>

<script type="text/javascript">
  $('input').change(function () {
      $('#settings-not-save').slideDown();
      $('.alert-success').slideUp();
      modificationForm(this);
  })
  $('textarea').change(function () {
      $('#settings-not-save').slideDown();
      $('.alert-success').slideUp();
      modificationForm(this);
  })
  $('select').change(function () {
      $('#settings-not-save').slideDown();
      $('.alert-success').slideUp();
      modificationForm(this);
  })
  $('.panel-body a.btn').click(function(e) {
    if (!confirm("Вы уверены?"))
      e.preventDefault();
  });
</script>