<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/*
    #####################################################################
    #############                                         ###############
    #############          PhpView를 호출하는 Router        ###############
    #############                                         ###############
    #####################################################################
 */
$routes->get('/', 'PhpViewController::mainCalendarView');
$routes->get('/login', 'PhpViewController::loginView');
$routes->get('/join', 'PhpViewController::joinView');

$routes->get('/schedule/register-view', 'PhpViewController::scheduleRegisterView');
$routes->get('/schedule/edit-view', 'PhpViewController::scheduleEditView');

$routes->get('/statistics/view', 'PhpViewController::statisticsView');

/*
    #####################################################################
    #############                                         ###############
    #############          Rest Api를 호출하는 Router       ###############
    #############                                         ###############
    #####################################################################
 */
