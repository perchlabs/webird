'use strict';

var promise;
export default function(iterable) {
  if (iterable) {
    if (promise) {
        throw 'Promise is already set.';
    }

    promise = Promise.all(iterable);
  }

  return promise;
}
