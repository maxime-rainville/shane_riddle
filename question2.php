<?php

const LIMIT = 100;

$primes = [];

// Get the prime numbers in descending order
find_primes($primes);
// $primes = array_reverse($primes);
// Initialize
$best_sets = [];
$x=LIMIT;

// Loop over all sets of 5 primes
foreach(subsets_of($primes, 5) as $subset) {
  $count = max_count_for_set(array_reverse($subset), $x, $toughest_sums);

  // If we get a count back, this set is valid and can at least equal the current coin count
  if (!($count === false)) {
    if ($count < $x) {
      // We've beaten the current best, let's reset things
      $x = $count;
      $best_sets = [[$subset,$toughest_sums]];
    } else {
      // We've equal the best
      $best_sets[] = [$subset,$toughest_sums];
    }
  }

}

echo "X=$x\n";
foreach ($best_sets as $answer) {
  echo implode($answer[0], ' ') . "\t ( ". implode($answer[1], ' ') . " )\n";
}

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
function max_count_for_set($primes, $upper_limit = LIMIT, &$toughest_sums) {

  $coin_count = 0;
  $toughest_sums = [];
  for ($i = 2; $i <= LIMIT; $i++) {
    $new_count = count_coins_needed_to_get_sum($primes, $i);
    if ($new_count === false || $new_count>$upper_limit) {
      return false;
    }

    if ($new_count > $coin_count) {
      $coin_count = $new_count;
      $toughest_sums = [$i];
    } elseif ($new_count == $coin_count) {
      $toughest_sums[] = $i;
    }
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

function subsets_of($primes, $n) {
  if ($n == 0) {
     yield [];
  } else {
      $n--;

      while (sizeof($primes) > $n) {
        $item = [array_shift($primes)];
        $subsets = subsets_of($primes, $n);
        foreach ($subsets as $subset) {
          yield array_merge($item, $subset);
        }
      }
  }
}
