# Launch Unit-Test At Once!
#
# @author      Linvo <linvo@foxmail.com>
# @copyright   2015 Linvo
# @version     0.0.1
# @package     tests
#
# NOTICE: 
# 1. Require the PHPUnit framework;
# 2. The filename of unit-test must begin with `test`;
# 3. Set 'debug_mode' to False in the conf/env.php;
# 3. Launch this file in its own directory.


find . -name "test2*.php" | while read file
do
    echo ---------------------------------------------------
    echo $file
    phpunit --bootstrap bootstrap.php $file
done
