const arr = Buffer.alloc(512);
crypto.getRandomValues(arr);

console.log(crypto.randomUUID());