<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_NameYourPriceGraphQl
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

declare(strict_types=1);

namespace Mageplaza\NameYourPriceGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\NameYourPrice\Helper\Data;
use Mageplaza\NameYourPrice\Model\Api\BargainManagement;
use Mageplaza\NameYourPrice\Model\RequestsFactory;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Mageplaza\NameYourPrice\Model\Api\ConfigManagement;

/**
 * Class AbstractResolver
 * @package Mageplaza\NameYourPriceGraphQl\Model\Resolver
 */
class AbstractResolver implements ResolverInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var BargainManagement
     */
    protected $bargainManagement;

    /**
     * @var RequestsFactory
     */
    protected $requestsFactory;

    /**
     * @var GetCustomer
     */
    protected $getCustomer;

    /**
     * @var int
     */
    protected $customerId;

    /**
     * @var ConfigManagement
     */
    protected $configManagement;

    /**
     * AbstractResolver constructor.
     *
     * @param Data $helperData
     * @param BargainManagement $bargainManagement
     * @param RequestsFactory $requestsFactory
     * @param GetCustomer $getCustomer
     * @param ConfigManagement $configManagement
     */
    public function __construct(
        Data $helperData,
        BargainManagement $bargainManagement,
        RequestsFactory $requestsFactory,
        GetCustomer $getCustomer,
        ConfigManagement $configManagement
    ) {
        $this->helperData        = $helperData;
        $this->bargainManagement = $bargainManagement;
        $this->requestsFactory   = $requestsFactory;
        $this->getCustomer       = $getCustomer;
        $this->configManagement  = $configManagement;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$this->helperData->isEnabled()) {
            throw new GraphQlNoSuchEntityException(__('Module is disabled.'));
        }

        if ($this->helperData->versionCompare('2.3.3')) {
            if ($context->getExtensionAttributes()->getIsCustomer() === false) {
                throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
            }
        }

        $customer         = $this->getCustomer->execute($context);
        $this->customerId = $customer->getId();
    }
}
