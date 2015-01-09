<?php

/**
 * Parent theme class
 */
require_once CLASS_DIR . '/blackcat/view/class.Theme.php';

final class Theme extends BCTheme
{
   /**
    * Wrap content
    */
   private $_wrapContent = true;

    /**
     * Constructor.
     * Create an instance of this class
     *
     * @param string $theme Theme name
     *
     * @return void
     */
    public function __construct($theme)
    {
       parent::__construct($theme);
    }

    public function wrapContent($wrap=true)
    {
      $this->_wrapContent = $wrap;
    }

    /**
     * Render html content
     *
     * @param string $content HTML content
     *
     * @return void
     */
    public function render($content)
    {
        echo $this->getHTML($content);
    }

    /**
     * Get html content
     *
     * @param string $content HTML content
     *
     * @return void
     */
    public function getHTML($content, $wrap = true)
    {
        //make additional formatting here
        return $content;
    }

    private function getMenu()
    {
        $sessObj    = Registry::get('session');

        if (!$sessObj->getUserID()) return '';

        $userObj    = Registry::get('user');

        $userId     = $sessObj->getUserID();
        $group      = $userObj->getGroup($userId);

        $menuList   = $userObj->getMenuList($group->group_id);
        $menus      = '';

        $url        = $_SERVER['REQUEST_URI'];
        $url        = explode("/", $url);
        $url        = $url[2];
        
        for ($i = 0; $i < sizeof($menuList); $i++) {
        
            if($menuList[$i]->menu_name == $url){
                $menu = " <li>
                            <a href='[--ROOT_URL--]/". $menuList[$i]->menu_url ."' class='active'>
                                ". $menuList[$i]->menu_desc ."
                            </a>
                         </li>
                        ";
            }
            else{
               $menu = " <li>
                            <a href='[--ROOT_URL--]/". $menuList[$i]->menu_url ."'>
                                ". $menuList[$i]->menu_desc ."
                            </a>
                         </li>
                        ";
            }

            $menus .= $menu;
        }

        return $menus;
    }

    private function getHeadContent(){

        $sessObj    = Registry::get('session');

        if (!$sessObj->getVar("accessToken")) return '';

        $userObj    = Registry::get('user');

        $userId     = $sessObj->getUserID();
        $getDetail  = $userObj->getDetail($userId);

        $username   = HTTP::getVar("username");
        $password   = HTTP::getVar("password");
              
        $Agen  =  new Agen();
        $return_value = $Agen->getProfile($username,$password);               
        $decode = json_decode($return_value);

        // echo $return_value;
        // exit();

        if ($decode->tiketux->status=="OK") {
      
        $head = "
                <table>
                    <tr>
                        <td>
                            <div class='head-user'>
                                Selamat Datang, <strong>". $decode->tiketux->results->userName ."</strong><br>
                                <div class='setting'><a href='[--ROOT_URL--]/dashboard/setting'><img src='[--ROOT_URL--]/themes/[--THEME--]/images/setting-3.png'>Setting</a></div>
                                <div class='logout'><a href='[--ROOT_URL--]/logout'><img src='[--ROOT_URL--]/themes/[--THEME--]/images/logout-3.png'>Logout</a></div>
                            </div>
                        </td>
                    </tr>
                </table>
                ";
        }
        return $head;

    }

    /**
     * Get error message.
     *
     * @return string error message
     */
   private function _getError()
   {
      $errors     = Error::getAll();
        $errorStr   = '';

      if (sizeof($errors)) {
         $errorStr   = '<div style="margin:5px"><b>DEBUGGING</b><br>';
         $errorStr   .= '<ul>';
         foreach ($errors as $key => $val) {
            $errorStr .= "<li><b>$key</b></li>";

            for ($i = 0; $i < sizeof($val); $i++) {
               $errorStr .= "$val[$i]";
            }
         }
         $errorStr .= '</ul></div>';
      }

      return $errorStr;
   }

    /**
     * Get html page
     *
     * @return string Complete html page
     */
   public function toString()
   {
        global $cfg;

        $html      = ob_get_contents();
        $html      = ($this->_wrapContent) ? "<div class='wrap'>$html</div>" : $html;

        ob_end_clean();

        $sessObj    = Registry::get('session');
        $qs         = new QueryString();

        $title      = (!empty($this->pageTitle)) ? $this->pageTitle . " - " . $cfg['sys']['websiteTitle'] : $cfg['sys']['websiteTitle'];
        $menus      = $this->getMenu();
        $head       = $this->getHeadContent();
        $saldo      = "";
        $sitemap    = "";
        $pagetitle  = "";

        /* Page Title */
        if(!empty($this->pageTitle)){
            
            $pagetitle = $this->pageTitle;

            /* Site Map */
            $countsite  = explode(" ", $this->pageTitle);

            if(count($countsite) == 1){
                $sitemap    = "Administrasi <span>&raquo;</span> ". $this->pageTitle ;
            }
            else{
                for($i = count($countsite) - 1 ; $i >= 0 ; $i --){
                    $site .= " <span>&raquo;</span> ". $countsite[$i] ;
                }
                $sitemap .= " Administrasi ". $site;
            }
        }
        else{
            $pagetitle  = "Halaman Tidak Tersedia";
            $sitemap    = "Administrasi <span>&raquo;</span> Halaman tidak tersedia";
        }

        $rview      = new View();

        $rview->setPath(THEME_DIR . '/' . $this->_theme . '/templates');
        $rview->setTemplate(((!empty($this->_rootTmpl)) ? $this->_rootTmpl : 'root'));

        $rview->setValue('DEBUG',            $this->_getError());
        $rview->setValue('HEAD',             $head);
        $rview->setValue('MENU',             $menus);
        $rview->setValue('USER',             $sessObj->getUserName());
        $rview->setValue('CONTENT',          $html);
        $rview->setValue('ROOT_URL',         ROOT_URL);
        $rview->setValue('THEME',            $this->_theme);
        $rview->setValue('CUR_URL',          ROOT_URL . HTTP::getCurrentURL());
        $rview->setValue('CUR_URL_PATH',     ROOT_URL . HTTP::getCurrentURLPath());
        $rview->setValue('QUERY_STRING',     $qs->toString());
        $rview->setValue('BODYSCRIPT',       $this->parseBodyScript());
        $rview->setValue('TITLE',            $title);
        $rview->setValue('JS_INCLUDE',       $this->parseJavaScript());
        $rview->setValue('CSS_INCLUDE',      $this->parseCSS());
        $rview->setValue('PAGE_TITLE',       $pagetitle);
        $rview->setValue('PAGE_SITE',        $sitemap);
        $rview->setValue('SALDO',            $saldo);

        $rview->parse('root');

        return $rview->getContents();
    }
}
?>