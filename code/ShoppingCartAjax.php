<?php
/**
 * Ajax-specific functionality for shopping cart controller
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 04.07.2014
 * @package shop
 * @subpackage ajax
 */
class ShoppingCartAjax extends Extension
{

    /**
     * @param SS_HTTPRequest $request
     * @param AjaxHTTPResponse $response
     * @param Buyable $product [optional]
     * @param int $quantity [optional]
     */
    public function updateAddResponse(&$request, &$response, $product=null, $quantity=1)
    {
        if ($request->isAjax()) {
            if (!$response) {
                $response = $this->owner->getAjaxResponse();
            }

            // Because ShoppingCart::current() calculates the order once and
            // then remembers the total, and that was called BEFORE the product
            // was added, we need to recalculate again here. Under non-ajax
            // requests the redirect eliminates the need for this but under
            // ajax the total lags behind the subtotal without this.
            ShoppingCart::curr()->calculate();

            $this->setupRenderContexts($response, $product);
            $response->pushRegion('SideCart', $this->owner);
            $response->triggerEvent('cartadd');
            $response->triggerEvent('cartchange', array(
                'action'    => 'add',
                'id'        => $product->ID,
                'quantity'  => $quantity,
            ));

            if (self::config()->show_ajax_messages && $this->owner->cart) {
                $response->triggerEvent('statusmessage', array(
                    'content'   => $this->owner->cart->getMessage(),
                    'type'      => $this->owner->cart->getMessageType(),
                ));
                $this->owner->cart->clearMessage();
            }
        }
    }


    /**
     * @param SS_HTTPRequest $request
     * @param AjaxHTTPResponse $response
     * @param Buyable $product [optional]
     * @param int $quantity [optional]
     */
    public function updateRemoveResponse(&$request, &$response, $product=null, $quantity=1)
    {
        if ($request->isAjax()) {
            if (!$response) {
                $response = $this->owner->getAjaxResponse();
            }

            // Because ShoppingCart::current() calculates the order once and
            // then remembers the total, and that was called BEFORE the product
            // was added, we need to recalculate again here. Under non-ajax
            // requests the redirect eliminates the need for this but under
            // ajax the total lags behind the subtotal without this.
            $order = ShoppingCart::curr();
            $order->calculate();

            $this->setupRenderContexts($response, $product);
            $response->pushRegion('SideCart', $this->owner);
            $response->triggerEvent('cartremove');
            $response->triggerEvent('cartchange', array(
                'action'    => 'remove',
                'id'        => $product->ID,
                'quantity'  => $quantity,
            ));

            if (self::config()->show_ajax_messages && $this->owner->cart) {
                $response->triggerEvent('statusmessage', array(
                    'content'   => $this->owner->cart->getMessage(),
                    'type'      => $this->owner->cart->getMessageType(),
                ));
                $this->owner->cart->clearMessage();
            }

            // This allows clientside scripts to redirect away from the cart/checkout pages if desired
            if (!$order || !$order->Items()->exists()) {
                $response->triggerEvent('cartempty');
            }
        }
    }


    /**
     * @param SS_HTTPRequest $request
     * @param AjaxHTTPResponse $response
     * @param Buyable $product [optional]
     */
    public function updateRemoveAllResponse(&$request, &$response, $product=null)
    {
        if ($request->isAjax()) {
            if (!$response) {
                $response = $this->owner->getAjaxResponse();
            }

            // Because ShoppingCart::current() calculates the order once and
            // then remembers the total, and that was called BEFORE the product
            // was added, we need to recalculate again here. Under non-ajax
            // requests the redirect eliminates the need for this but under
            // ajax the total lags behind the subtotal without this.
            $order = ShoppingCart::curr();
            $order->calculate();

            $this->setupRenderContexts($response, $product);
            $response->pushRegion('SideCart', $this->owner);
            $response->triggerEvent('cartremove');
            $response->triggerEvent('cartchange', array(
                'action'    => 'removeall',
                'id'        => $product,
                'quantity'  => 0,
            ));

            if (self::config()->show_ajax_messages && $this->owner->cart) {
                $response->triggerEvent('statusmessage', array(
                    'content'   => $this->owner->cart->getMessage(),
                    'type'      => $this->owner->cart->getMessageType(),
                ));
                $this->owner->cart->clearMessage();
            }

            // This allows clientside scripts to redirect away from the cart/checkout pages if desired
            if (!$order || !$order->Items()->exists()) {
                $response->triggerEvent('cartempty');
            }
        }
    }


    /**
     * @param SS_HTTPRequest $request
     * @param AjaxHTTPResponse $response
     * @param Buyable $product [optional]
     * @param int $quantity [optional]
     */
    public function updateSetQuantityResponse(&$request, &$response, $product=null, $quantity=1)
    {
        if ($request->isAjax()) {
            if (!$response) {
                $response = $this->owner->getAjaxResponse();
            }

            // Because ShoppingCart::current() calculates the order once and
            // then remembers the total, and that was called BEFORE the product
            // was added, we need to recalculate again here. Under non-ajax
            // requests the redirect eliminates the need for this but under
            // ajax the total lags behind the subtotal without this.
            $order = ShoppingCart::curr();
            $order->calculate();

            $this->setupRenderContexts($response, $product);
            $response->pushRegion('SideCart', $this->owner);
            $response->triggerEvent('cartquantity');
            $response->triggerEvent('cartchange', array(
                'action'    => 'setquantity',
                'id'        => $product->ID,
                'quantity'  => $quantity,
            ));

            if (self::config()->show_ajax_messages && $this->owner->cart) {
                $response->triggerEvent('statusmessage', array(
                    'content'   => $this->owner->cart->getMessage(),
                    'type'      => $this->owner->cart->getMessageType(),
                ));
                $this->owner->cart->clearMessage();
            }

            // This allows clientside scripts to redirect away from the cart/checkout pages if desired
            if (!$order || !$order->Items()->exists()) {
                $response->triggerEvent('cartempty');
            }
        }
    }


    /**
     * @param SS_HTTPRequest $request
     * @param AjaxHTTPResponse $response
     */
    public function updateClearResponse(&$request, &$response)
    {
        if ($request->isAjax()) {
            if (!$response) {
                $response = $this->owner->getAjaxResponse();
            }

            // Because ShoppingCart::current() calculates the order once and
            // then remembers the total, and that was called BEFORE the product
            // was added, we need to recalculate again here. Under non-ajax
            // requests the redirect eliminates the need for this but under
            // ajax the total lags behind the subtotal without this.
            ShoppingCart::curr()->calculate();

            $this->setupRenderContexts($response);
            $response->pushRegion('SideCart', $this->owner);
            $response->triggerEvent('cartempty'); // this is triggered any time the cart has no items in it
            $response->triggerEvent('cartchange', array(
                'action'    => 'clear',
            ));

            if (self::config()->show_ajax_messages && $this->owner->cart) {
                $response->triggerEvent('statusmessage', array(
                    'content'   => $this->owner->cart->getMessage(),
                    'type'      => $this->owner->cart->getMessageType(),
                ));
                $this->owner->cart->clearMessage();
            }
        }
    }


    /**
     * Adds the ajax class to the VariationForm
     */
    public function updateVariationForm()
    {
        $this->owner->addExtraClass('ajax');
    }


    /**
     * @param SS_HTTPRequest $request
     * @param AjaxHTTPResponse $response
     * @param Buyable $variation [optional]
     * @param int $quantity [optional]
     * @param VariationForm $form [optional]
     */
    public function updateVariationFormResponse(&$request, &$response, $variation=null, $quantity=1, $form=null)
    {
        if ($request->isAjax()) {
            if (!$response) {
                $response = $this->owner->getAjaxResponse();
            }

            // Because ShoppingCart::current() calculates the order once and
            // then remembers the total, and that was called BEFORE the product
            // was added, we need to recalculate again here. Under non-ajax
            // requests the redirect eliminates the need for this but under
            // ajax the total lags behind the subtotal without this.
            ShoppingCart::curr()->calculate();

            $this->setupRenderContexts($response, $variation);
            $response->addRenderContext('FORM', $form);
            $response->pushRegion('SideCart', $this->owner);
            $response->triggerEvent('cartadd');
            $response->triggerEvent('cartchange', array(
                'action'    => 'add',
                'id'        => $variation->ID,
                'quantity'  => $quantity,
            ));

            if (self::config()->show_ajax_messages) {
                $response->triggerEvent('statusmessage', array(
                    'content'   => $form->Message(),
                    'type'      => $form->MessageType(),
                ));
                $form->clearMessage();
            }
        }
    }


    /**
    * Adds the ajax class to the AddProductForm
    */
    public function updateAddProductForm()
    {
        $this->owner->addExtraClass('ajax');
    }


    /**
    * @param SS_HTTPRequest $request
    * @param AjaxHTTPResponse $response
    * @param Buyable $buyable [optional]
    * @param int $quantity [optional]
    * @param AddProductForm $form [optional]
    */
    public function updateAddProductFormResponse(&$request, &$response, $buyable=null, $quantity=1, $form=null)
    {
        if ($request->isAjax()) {
            if (!$response) {
                $response = $this->owner->getAjaxResponse();
            }

            // Because ShoppingCart::current() calculates the order once and
            // then remembers the total, and that was called BEFORE the product
            // was added, we need to recalculate again here. Under non-ajax
            // requests the redirect eliminates the need for this but under
            // ajax the total lags behind the subtotal without this.
            ShoppingCart::curr()->calculate();

            $this->setupRenderContexts($response, $buyable);
            $response->addRenderContext('FORM', $form);
            $response->pushRegion('SideCart', $this->owner);
            $response->triggerEvent('cartadd');
            $response->triggerEvent('cartchange', array(
                'action'    => 'add',
                'id'        => $buyable->ID,
                'quantity'  => $quantity,
            ));

            if (self::config()->show_ajax_messages && $this->owner->cart) {
                $response->triggerEvent('statusmessage', array(
                    'content'   => $this->owner->cart->getMessage(),
                    'type'      => $this->owner->cart->getMessageType(),
                ));
                $form->clearMessage();
            }
        }
    }


    /**
     * Adds the ajax class to the CartForm
     */
    public function updateCartForm(&$form, $cart)
    {
        $form->addExtraClass('ajax');
        $form->setAttribute('data-ajax-region', 'CartFormAjax');
    }


    /**
     * @param SS_HTTPRequest $request
     * @param AjaxHTTPResponse $response
     * @param AddProductForm $form [optional]
     */
    public function updateCartFormResponse(&$request, &$response, $form=null)
    {
        if ($request->isAjax()) {
            if (!$response) {
                $response = $this->owner->getAjaxResponse();
            }

            // Because ShoppingCart::current() calculates the order once and
            // then remembers the total, and that was called BEFORE the product
            // was added, we need to recalculate again here. Under non-ajax
            // requests the redirect eliminates the need for this but under
            // ajax the total lags behind the subtotal without this.
            ShoppingCart::curr()->calculate();

            $this->setupRenderContexts($response);

            if (self::config()->show_ajax_messages) {
                $response->triggerEvent('statusmessage', array(
                    'content'   => $form->Message(),
                    'type'      => $form->MessageType(),
                ));
                $form->clearMessage();
            }

            $response->pushRegion('CartFormAjax', $this->owner, array('Editable' => true));
        }
    }


    /**
     * Adds some standard render contexts for pulled regions.
     *
     * @param AjaxHTTPResponse $response
     * @param Buyable $buyable [optional]
     */
    protected function setupRenderContexts(AjaxHTTPResponse $response, $buyable=null)
    {
        if ($this->owner->hasMethod('Cart')) {
            $cart = $this->owner->Cart();
            if ($cart instanceof ViewableData) {
                $response->addRenderContext('CART', $this->owner->Cart());
            }
        }

        if ($buyable) {
            $response->addRenderContext('BUYABLE', $buyable);

            // this could be a Product or ProductVariation (or something else)
            // but we want a render target available for the product specifically
            // for rendering ProductGroupItem in a category or search view
            if ($buyable instanceof Product) {
                $response->addRenderContext('PRODUCT', $buyable);
            } elseif ($buyable->hasMethod('Product')) {
                $response->addRenderContext('PRODUCT', $buyable->Product());
            }
        }
    }

    /**
     * Helper for getting static shop config.
     * The 'config' static function isn't avaialbe on Extensions.
     * @return Config_ForClass configuration object
     */
    public static function config()
    {
        return new Config_ForClass("ShoppingCartAjax");
    }
}
