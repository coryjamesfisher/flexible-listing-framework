node {
   // Mark the code checkout 'stage'....
   stage 'Checkout'

   // Checkout code from repository
   checkout scm

   // Mark the code build 'stage'....
   stage 'Dependencies'
   sh "composer install"
   
   stage 'Build'
   sh "./vendor/bin/phpunit --log-junit 'reports/unitreport.xml' --coverage-html 'reports/coverage' --coverage-clover 'reports/coverage/coverage.xml' test/"
}
