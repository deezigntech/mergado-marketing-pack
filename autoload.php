<?php

/**
 * SERVICES
 */

include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Glami/GlamiPixelService.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Glami/GlamiTopService.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Facebook/FacebookService.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Google/GoogleAnalyticsRefundService.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Google/GoogleAdsService.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Google/GoogleTagManagerService.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Google/GoogleReviews/GoogleReviewsService.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Kelkoo/KelkooService.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Heureka/HeurekaService.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'NajNakup/NajNakup.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'NajNakup/NajNakupService.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Pricemania/Pricemania.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Pricemania/PricemaniaService.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Zbozi/Zbozi.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Zbozi/ZboziService.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Arukereso/ArukeresoService.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Etarget/EtargetService.php');

/**
 * SERVICE - INTEGRATIONS
 */
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Etarget/integration/EtargetServiceIntegration.php');
include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . 'Kelkoo/integration/KelkooServiceIntegration.php');

/**
 * HELPERS
 */

include_once wp_normalize_path(__MERGADO_TOOLS_DIR__ . 'TemplateLoader.php');


include_once wp_normalize_path(__MERGADO_TOOLS_DIR__ . 'CronsClass.php');
include_once wp_normalize_path(__MERGADO_TOOLS_DIR__ . 'NewsClass.php');
include_once wp_normalize_path(__MERGADO_TOOLS_DIR__ . 'ToolsClass.php');
include_once wp_normalize_path(__MERGADO_TOOLS_DIR__ . 'BannersClass.php');
include_once wp_normalize_path(__MERGADO_TOOLS_DIR__ . 'SettingsClass.php');
include_once wp_normalize_path(__MERGADO_TOOLS_DIR__ . 'LanguagesClass.php');
include_once wp_normalize_path(__MERGADO_TOOLS_DIR__ . 'CookieClass.php');

include_once wp_normalize_path(__MERGADO_TOOLS_XML_DIR__ . 'helpers/EanClass.php');


include_once wp_normalize_path(__MERGADO_TOOLS_DIR__ . 'RssClass.php');
include_once wp_normalize_path(__MERGADO_TOOLS_DIR__ . 'XMLClass.php');
include_once wp_normalize_path(__MERGADO_TOOLS_XML_DIR__ . 'XMLStockFeed.php');
include_once wp_normalize_path(__MERGADO_TOOLS_XML_DIR__ . 'XMLCategoryFeed.php');
include_once wp_normalize_path(__MERGADO_TOOLS_XML_DIR__ . 'XMLProductFeed.php');


include_once wp_normalize_path(__MERGADO_TOOLS_DIR__ . 'ImportPricesClass.php');
include_once wp_normalize_path(__MERGADO_TOOLS_DIR__ . 'AlertClass.php');

include_once wp_normalize_path(__MERGADO_TOOLS_XML_DIR__ . 'helpers/ean/CeskeSluzby.php');
include_once wp_normalize_path(__MERGADO_TOOLS_XML_DIR__ . 'helpers/ean/EanForWoocommerce.php');
include_once wp_normalize_path(__MERGADO_TOOLS_XML_DIR__ . 'helpers/ean/ProductGtinEanUpcIsbn.php');
include_once wp_normalize_path(__MERGADO_TOOLS_XML_DIR__ . 'helpers/ean/WooAddGtin.php');
include_once wp_normalize_path(__MERGADO_TOOLS_XML_DIR__ . 'helpers/ean/WpssoWcMetadata.php');


include_once wp_normalize_path(__MERGADO_SERVICES_DIR__ . '/Arukereso/TrustedShop.php');