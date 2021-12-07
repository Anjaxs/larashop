<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // base tables
        \Encore\Admin\Auth\Database\Menu::truncate();
        \Encore\Admin\Auth\Database\Menu::insert(
            [
                [
                    "parent_id" => 0,
                    "order" => 1,
                    "title" => "首页",
                    "icon" => "fa-bar-chart",
                    "uri" => "/",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 5,
                    "title" => "系统管理",
                    "icon" => "fa-tasks",
                    "uri" => NULL,
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 6,
                    "title" => "管理员",
                    "icon" => "fa-users",
                    "uri" => "auth/users",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 7,
                    "title" => "角色",
                    "icon" => "fa-user",
                    "uri" => "auth/roles",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 8,
                    "title" => "权限",
                    "icon" => "fa-ban",
                    "uri" => "auth/permissions",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 9,
                    "title" => "菜单",
                    "icon" => "fa-bars",
                    "uri" => "auth/menu",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 11,
                    "title" => "操作日志",
                    "icon" => "fa-history",
                    "uri" => "auth/logs",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 2,
                    "title" => "用户管理",
                    "icon" => "fa-users",
                    "uri" => "/users",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 3,
                    "title" => "商品管理",
                    "icon" => "fa-cubes",
                    "uri" => "/products",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 10,
                    "title" => "队列任务",
                    "icon" => "fa-server",
                    "uri" => "horizon",
                    "permission" => "horizon"
                ],
                [
                    "parent_id" => 0,
                    "order" => 4,
                    "title" => "订单管理",
                    "icon" => "fa-rmb",
                    "uri" => "/orders",
                    "permission" => NULL
                ]
            ]
        );

        \Encore\Admin\Auth\Database\Permission::truncate();
        \Encore\Admin\Auth\Database\Permission::insert(
            [
                [
                    "name" => "所有权限",
                    "slug" => "*",
                    "http_method" => "",
                    "http_path" => "*"
                ],
                [
                    "name" => "仪表盘",
                    "slug" => "dashboard",
                    "http_method" => "GET",
                    "http_path" => "/"
                ],
                [
                    "name" => "登录",
                    "slug" => "auth.login",
                    "http_method" => "",
                    "http_path" => "/auth/login\r\n/auth/logout"
                ],
                [
                    "name" => "用户设置",
                    "slug" => "auth.setting",
                    "http_method" => "GET,PUT",
                    "http_path" => "/auth/setting"
                ],
                [
                    "name" => "Auth management",
                    "slug" => "auth.management",
                    "http_method" => "",
                    "http_path" => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs"
                ],
                [
                    "name" => "用户管理",
                    "slug" => "users",
                    "http_method" => "",
                    "http_path" => "/users*"
                ],
                [
                    "name" => "队列任务",
                    "slug" => "horizon",
                    "http_method" => "",
                    "http_path" => "/admin/horizon*"
                ]
            ]
        );

        \Encore\Admin\Auth\Database\Role::truncate();
        \Encore\Admin\Auth\Database\Role::insert(
            [
                [
                    "name" => "Administrator",
                    "slug" => "administrator"
                ],
                [
                    "name" => "运营",
                    "slug" => "operation"
                ]
            ]
        );

        // pivot tables
        DB::table('admin_role_menu')->truncate();
        DB::table('admin_role_menu')->insert(
            [
                [
                    "role_id" => 1,
                    "menu_id" => 2
                ],
                [
                    "role_id" => 1,
                    "menu_id" => 10
                ]
            ]
        );

        DB::table('admin_role_permissions')->truncate();
        DB::table('admin_role_permissions')->insert(
            [
                [
                    "role_id" => 1,
                    "permission_id" => 1
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 2
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 3
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 4
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 6
                ]
            ]
        );

        // finish
    }
}
