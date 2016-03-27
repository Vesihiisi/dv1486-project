<?php
/**
 * Config-file for navigation bar.
 *
 */
return [

    // Use for styling the menu
    'class' => 'navbar',
 
    // Here comes the menu strcture
    'items' => [

        // This is a menu item
        'home'  => [
            'text'  => '<i class="fa fa-home"></i>Front page',
            'url'   => $this->di->get('url')->create(''),
            'title' => ''
        ],
        'comment' => [
            'text'  => '<i class="fa fa-question"></i>Questions',
            'url'   => $this->di->get('url')->create('comment'),
            'title' => ''
        ],
        'tag' => [
            'text'  => '<i class="fa fa-tags"></i>Tags',
            'url'   => $this->di->get('url')->create('tag'),
            'title' => ''
        ],
        'users'  => [
            'text'  => '<i class="fa fa-users"></i>Users',
            'url'   => $this->di->get('url')->create('users'),
            'title' => ''
        ],
                'about'  => [
            'text'  => '<i class="fa fa-square"></i>
</i>About',
            'url'   => $this->di->get('url')->create('about'),
            'title' => ''
        ],
    ],
 


    /**
     * Callback tracing the current selected menu item base on scriptname
     *
     */
    'callback' => function ($url) {
        if ($url == $this->di->get('request')->getCurrentUrl(false)) {
            return true;
        }
    },



    /**
     * Callback to check if current page is a decendant of the menuitem, this check applies for those
     * menuitems that has the setting 'mark-if-parent' set to true.
     *
     */
    'is_parent' => function ($parent) {
        $route = $this->di->get('request')->getRoute();
        return !substr_compare($parent, $route, 0, strlen($parent));
    },



   /**
     * Callback to create the url, if needed, else comment out.
     *
     */
   /*
    'create_url' => function ($url) {
        return $this->di->get('url')->create($url);
    },
    */
];
