<?php

echo \go1\util_dataset\staff\es_dumper\ElasticSearchAccountDumper::dump($app['go1.client.es'], $portalId);
