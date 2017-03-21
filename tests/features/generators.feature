Feature: Generators

  Scenario Outline: Generation
    When I generate a <command> with "<argument>"
    Then I should see "Created:"
    And "<generatedFilePath>" should match my stub

    Examples:
      | command    | argument         | generatedFilePath                                               |
      | model      | Order            | workbench/lilil/generators/tests/tmp/Order.php                    |
      | seed       | recent_orders    | workbench/lilil/generators/tests/tmp/RecentOrdersTableSeeder.php  |
      | controller | OrdersController | workbench/lilil/generators/tests/tmp/OrdersController.php         |
      | view       | orders.bar.index | workbench/lilil/generators/tests/tmp/orders/bar/index.blade.php   |
