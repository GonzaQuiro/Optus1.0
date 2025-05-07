<?php

namespace App\Http\Controllers\Lists;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Category;
use App\Models\Pais;
use App\Models\Area;
use App\Models\Provincia;
use App\Models\Ciudad;
use App\Models\UserType;
use App\Models\OffererCompany;
use App\Models\CustomerCompany;

class ListsController extends BaseController
{
    public function getCategoriesWithAreas(Request $request, Response $response)
    {
        $success = true;
        $message = 'OK';
        $status = 200;
        $list = [];

        try {
            $list = Category::getFromCategoryGroupList();
        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message,
            'data'      => [
                'list'  => $list
            ]
        ], $status);
    }

    public function getAreas(Request $request, Response $response)
    {
        $ids = $request->getQueryParams()['Categorias'];

        $success = true;
        $message = 'OK';
        $status = 200;
        $list = [];

        try {

            if ($ids) {
                $list = Area::getFromCategoryIdsList($ids);
            }

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message,
            'data'      => [
                'list'  => $list
            ]
        ], $status);
    }

    public function getCountriesWithProvincesList(Request $request, Response $response)
    {
        $success = true;
        $message = 'OK';
        $status = 200;
        $list = [];

        try {
            $list = Pais::getCountriesWithProvincesList();
        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message,
            'data'      => [
                'list'  => $list
            ]
        ], $status);
    }    

    public function getProvinces(Request $request, Response $response)
    {
        $ids = $request->getQueryParams()['Countries'];

        $success = true;
        $message = 'OK';
        $status = 200;
        $list = [];

        try {
            if ($ids) {
                $list = Provincia::getFromCountryIdsList($ids);
            }
        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message,
            'data'      => [
                'list'  => $list
            ]
        ], $status);
    }

    public function getCities(Request $request, Response $response)
    {
        $ids = $request->getQueryParams()['Provinces'];
        $success = true;
        $message = 'OK';
        $status = 200;
        $list = [];
        try {

            if ($ids) {
                $list = Ciudad::getFromProvinceIdsList($ids);
            }

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message,
            'data'      => [
                'list'  => $list
            ]
        ], $status);
    }

    public function getCompanies(Request $request, Response $response)
    {
        $type_id = (int) $request->getQueryParams()['TypeId'];
        $message = null;
        $status = 200;
        $list = [];

        try {
            $user_type = UserType::find((int) $type_id);

            if ($user_type->is_admin) {
                $list = [];
            } else if ($user_type->is_offerer) {
                $list = OffererCompany::getList();
            } else if ($user_type->is_customer) {
                $list = CustomerCompany::getList();
            }
            
            $success = true;

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message,
            'data'      => [
                'list'  => $list
            ]
        ], $status);
    }
}