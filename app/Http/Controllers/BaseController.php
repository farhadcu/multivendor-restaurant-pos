<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\FlashMessages;
use App\Helpers\Helper;//our own custom helper class

class BaseController extends Controller
{
    use FlashMessages;
    /**
     * @var null
     */
    protected $data   = null;
    protected $output = null;
    protected $helper; 

    public function __construct()
    {
        //load our custom helper in parent constructor
        $this->middleware(function ($request, $next) {
            $this->helper = new Helper(); 
            return $next($request);
        });
    }
    /**
     * @param $title
     * @param $subTitle
     */
    protected function setPageData($page_title, $sub_title, $page_icon)
    {
        view()->share(['page_title' => $page_title, 'sub_title' => $sub_title, 'page_icon' => $page_icon]);
    }

    /**
     * @param int $errorCode
     * @param null $message
     * @return \Illuminate\Http\Response
     */
    protected function showErrorPage($errorCode = 404, $message = null)
    {
        $data['message'] = $message;
        return response()->view('errors.'.$errorCode, $data, $errorCode);
    }

    /**
     * @param bool $error
     * @param int $responseCode
     * @param array $message
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseJson($error = true, $responseCode = 200, $message = [], $data = null)
    {
        return response()->json([
            'error'         =>  $error,
            'response_code' => $responseCode,
            'message'       => $message,
            'data'          =>  $data
        ]);
    }

    /**
     * @param $route
     * @param $message
     * @param string $type
     * @param bool $error
     * @param bool $withOldInputWhenError
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function responseRedirect($route, $message, $type = 'info', $error = false, $withOldInputWhenError = false)
    {
        $this->setFlashMessage($message, $type);
        $this->showFlashMessages();

        if ($error && $withOldInputWhenError) {
            return redirect()->back()->withInput();
        }

        return redirect()->route($route);
    }

    /**
     * @param $message
     * @param string $type
     * @param bool $error
     * @param bool $withOldInputWhenError
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function responseRedirectBack($message,$type = 'info')
    {
        $this->setFlashMessage($message,$type);
        $this->showFlashMessages();

        return redirect()->back();
    }

    //access blocked message method
    protected function access_blocked()
    {
        $json['status']   = 'danger';
        $json['message']  = 'Unauthorize access blocked';
        return $json;
    }
}
