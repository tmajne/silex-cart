**App**

**Reqirements**

* php7.1-xml
* php7.1-mbstring
* php7.1-sqlite3
* $app['cart.storage.path'] - path must be writable

**API**

***Games*** 

* listing: GET: games?limit=20
* show one item: GET: games/{gameId}
* create: PUT: games/ - parameters "title" and "price"
* update: POST: games/{gameId} - parameters "title" or/and "price"
* remove: DELETE: games/{gameId}
* load test data: PUT: /games/load/test/data

***Cart***

* listing: GET: carts/admin?limit=20
* show on item: GET: carts/{cartId}
* create: PUT: carts/
* remove: DELETE: carts/{cartId}
* add item: PUT: carts/{cartId}/items/{itemId}?count=3
* update item quantity: POST: carts/{cartId}/items/{itemId}/{deltaQuantity}
* remove item: DELETE: carts/{cartId}/items/{itemId}