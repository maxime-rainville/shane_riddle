<?php

const LIMIT = 100;

$primes = [];

// Get the prime numbers in descending order
find_primes($primes);
$primes = array_reverse($primes);

// Calculate the X when we have a full set of prime numbers
$baseline = max_count_for_set($primes);
echo "X=$baseline \n";

// List of the prime numbers we can't do without
$needed = [];

// Try removing each prime and see if we can still achieve the baseline
while (!empty($primes)) {
  $test_without = array_shift($primes);
  $count = max_count_for_set(array_merge($needed, $primes), $baseline);
  // var_dump($count);
  if ($count === false) {
    $needed[] = $test_without;
  }
}

echo implode($needed, " ") . "\n";

/**
 * Get a list of prime numbers lower than LIMIT
 * @param  array  $primes List of prime numbers
 * @param  integer $lower minimum
 * @return [type]          [description]
 */
function find_primes(&$primes, $lower=0) {
  $prime = gmp_nextprime($lower);
  if ($prime > LIMIT) {
    return;
  } else {
    $primes[] = (int)$prime;
    find_primes($primes, $prime);
  }

}


/**
 * Find the maximum number of coins needed to achieve the SUM of any value in
 * the range from 2 to LIMIT given a set of prime numbers.
 * @param array $primes set of prime numbers
 * @param int $upper_limit return false if the value is over that LIMIT
 * @return int|boolean Number of coins needed or FALSE if the number of coins is
 * higher than the upper limit or if a value can't be computed with the given set.
 */
function max_count_for_set($primes, $upper_limit = LIMIT) {

  $coin_count=0;
  for ($i = 2; $i <= LIMIT; $i++) {
    $new_count = count_coins_needed_to_get_sum($primes, $i);
    if ($new_count>$upper_limit) {
      return false;
    }

    $coin_count = max($coin_count, $new_count);
  }

  return $coin_count;
}

/**
 * Calculate the number of coins needed to achieve a sum given a set.
 * @param  [type] $set [description]
 * @param  [type] $sum [description]
 * @return [type]      [description]
 */
function count_coins_needed_to_get_sum($set, $sum) {
  if ($sum == 0) {
    return 0;
  } elseif (empty($set)) {
    return false;
  }

  $prime = array_shift($set);
  $count = floor($sum / $prime);
  $reminder = $sum % $prime;
  if ($reminder > 0) {
    $reminder_count = false;

    $reminder_count = count_coins_needed_to_get_sum($set, $reminder);
    while ($reminder_count === false) {
      $reminder += $prime;
      $count--;
      if ($count < 0) {
        return false;
      } else {
        $reminder_count = count_coins_needed_to_get_sum($set, $reminder);
      }
    }


    $count += $reminder_count;
  }

  return $count;

}
