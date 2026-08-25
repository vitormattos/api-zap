Feature: Zap API

  Scenario: Populate with viewport
    Given I send "GET" to "/api/zap-search" using rows (200)
      | usageTypes | RESIDENTIAL |
      | business | RENTAL |
      | viewport | -43.27229506098763,-22.897444415987277\|-43.2885170611097,-22.90769809054536 |
      | levels | LANDING,UNIT_TYPE |

  Scenario: Populate with address
    Given I send "GET" to "/api/zap-search" using rows (200)
      | usageTypes | RESIDENTIAL |
      | business | RENTAL |
      | addressCity | Rio de Janeiro |
      | addressState | Rio de Janeiro |
