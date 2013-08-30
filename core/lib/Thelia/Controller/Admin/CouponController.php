<?php
/**********************************************************************************/
/*                                                                                */
/*      Thelia	                                                                  */
/*                                                                                */
/*      Copyright (c) OpenStudio                                                  */
/*      email : info@thelia.net                                                   */
/*      web : http://www.thelia.net                                               */
/*                                                                                */
/*      This program is free software; you can redistribute it and/or modify      */
/*      it under the terms of the GNU General Public License as published by      */
/*      the Free Software Foundation; either version 3 of the License             */
/*                                                                                */
/*      This program is distributed in the hope that it will be useful,           */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of            */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             */
/*      GNU General Public License for more details.                              */
/*                                                                                */
/*      You should have received a copy of the GNU General Public License         */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.      */
/*                                                                                */
/**********************************************************************************/

namespace Thelia\Controller\Admin;

use Thelia\Core\Event\Coupon\CouponCreateEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Security\Exception\AuthenticationException;
use Thelia\Core\Security\Exception\AuthorizationException;
use Thelia\Coupon\CouponRuleCollection;
use Thelia\Form\CouponCreationForm;
use Thelia\Model\Coupon;

/**
 * Created by JetBrains PhpStorm.
 * Date: 8/19/13
 * Time: 3:24 PM
 *
 * Control View and Action (Model) via Events
 *
 * @package Coupon
 * @author  Guillaume MOREL <gmorel@openstudio.fr>
 *
 */
class CouponController extends BaseAdminController
{
    /**
     * List all Coupons Action
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->process();
    }

    /**
     * Manage Coupons list display
     *
     * @param array $args GET arguments
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function browseCoupons($args)
    {
        $this->checkAuth("ADMIN", "admin.coupon.view");

        return $this->render('coupon/list', $args);
    }

    /**
     * Manage Coupons creation display
     *
     * @param array $args GET arguments
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function createCoupon($args)
    {
        $this->checkAuth("ADMIN", "admin.coupon.view");


        if ($this->getRequest()->isMethod('POST')) {

            $couponCreationForm = new CouponCreationForm($this->getRequest());

            $form = $this->validateForm($couponCreationForm, "POST");

            $data = $form->getData();

            $couponBeingCreated = new Coupon();
            $couponBeingCreated->setCode($data["code"]);
            $couponBeingCreated->setType($data["type"]);
            $couponBeingCreated->setTitle($data["title"]);
            $couponBeingCreated->setShortDescription($data["shortDescription"]);
            $couponBeingCreated->setDescription($data["description"]);
            $couponBeingCreated->setAmount($data["amount"]);
            $couponBeingCreated->setIsEnabled($data["isEnabled"]);
            $couponBeingCreated->setExpirationDate($data["expirationDate"]);
            $couponBeingCreated->setSerializedRules(
                new CouponRuleCollection(
                    array()
                )
            );
            $couponBeingCreated->setIsCumulative($data["isCumulative"]);
            $couponBeingCreated->setIsRemovingPostage($data["isRemovingPostage"]);
            $couponBeingCreated->setMaxUsage($data["maxUsage"]);
            $couponBeingCreated->setIsAvailableOnSpecialOffers($data["isAvailableOnSpecialOffers"]);

            $couponCreateEvent = new CouponCreateEvent(
                $couponBeingCreated
            );

            $this->dispatch(TheliaEvents::BEFORE_CREATE_COUPON, $couponCreateEvent);
            // @todo Save
            $this->adminLogAppend(
                sprintf(
                    'Coupon %s (ID %s) created',
                    $couponBeingCreated->getTitle(),
                    $couponBeingCreated->getId()
                )
            );
            $this->dispatch(TheliaEvents::AFTER_CREATE_COUPON, $couponCreateEvent);
        } else {

        }

        return $this->render('coupon/edit', $args);
    }

    /**
     * Manage Coupons edition display
     *
     * @param array $args GET arguments
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function editCoupon($args)
    {
        $this->checkAuth("ADMIN", "admin.coupon.view");

        return $this->render('coupon/edit', $args);
    }

    /**
     * Manage Coupons read display
     *
     * @param array $args GET arguments
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function readCoupon($args)
    {
        $this->checkAuth("ADMIN", "admin.coupon.view");

        return $this->render('coupon/read', $args);
    }

    /**
     * Process all Actions
     *
     * @param string $action Action to process
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function processAction()
    {
        // Get the current action
        $action = $this->getRequest()->get('action', 'browse');

        // Get the category ID
//        $id = $this->getRequest()->get('id', 0);

        $args = array(
            'action' 			  => $action,
//            'current_coupon_id'   => $id
        );

        try {
            switch ($action) {
                case 'browse' : // Browse coupon
                    return $this->browseCoupons($args);
                case 'create' : // Create a new coupon
                    return $this->createCoupon($args);
                case 'edit' : // Edit an existing coupon
                    return $this->editCoupon($args);
                case 'read' : // Read an existing coupon
                    return $this->readCoupon($args);
            }
        } catch (AuthorizationException $ex) {
            return $this->errorPage($ex->getMessage());
        } catch (AuthenticationException $ex) {
            return $this->errorPage($ex->getMessage());
        }

        // We did not recognized the action -> return a 404 page
        return $this->pageNotFound();
    }
}