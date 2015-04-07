<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchBundle\Tests\Functional\DSL\Aggregation;

use ONGR\ElasticsearchBundle\DSL\Aggregation\SumAggregation;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;

/**
 * Function tests for sum aggregation.
 */
class SumAggregationTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 1,
                        'title' => 'foo',
                        'price' => 10.45,
                    ],
                    [
                        '_id' => 2,
                        'title' => 'bar',
                        'price' => 32,
                    ],
                    [
                        '_id' => 3,
                        'title' => 'pizza',
                        'price' => 15.1,
                    ],
                ],
            ],
        ];
    }

    /**
     * Test for sum aggregation.
     */
    public function testMinAggregation()
    {
        /** @var Repository $repo */
        $repo = $this->getManager()->getRepository('AcmeTestBundle:Product');

        $aggregation = new SumAggregation('test_agg');
        $aggregation->setField('price');

        $search = $repo->createSearch()->addAggregation($aggregation);
        $results = $repo->execute($search, Repository::RESULTS_RAW);

        $expectedResult = [
            'agg_test_agg' => [
                'value' => 57.55,
            ],
        ];

        $this->assertArrayHasKey('aggregations', $results);
        $this->assertEquals($expectedResult, $results['aggregations'], '', 0.01);
    }

    /**
     * Test for sum aggregation when script is set.
     */
    public function testStatsAggregationWithScriptSet()
    {
        /** @var Repository $repo */
        $repo = $this->getManager()->getRepository('AcmeTestBundle:Product');

        $aggregation = new SumAggregation('test_agg');
        $aggregation->setField('price');
        $aggregation->setScript('_value * 1.2');

        $search = $repo->createSearch()->addAggregation($aggregation);
        $results = $repo->execute($search, Repository::RESULTS_RAW);
        $expectedResult = [
            'agg_test_agg' => [
                'value' => 69.06,
            ],
        ];
        $this->assertArrayHasKey('aggregations', $results, 'results array should have aggregations key');
        $this->assertEquals($expectedResult, $results['aggregations'], '', 0.01);
    }
}