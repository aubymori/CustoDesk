<?php
namespace CustoDesk\Page\Admin;

use CustoDesk\DB;
use CustoDesk\RequestMetadata;

class UpdateTablesController extends AdminPageController
{
    public string $template = "admin/update_tables";
    public string $title = "Tables Updated";

    public function onGet(RequestMetadata $request): bool
    {
        DB::update();
        return true;
    }
}