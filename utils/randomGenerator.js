const arr = Buffer.alloc(256);
crypto.getRandomValues(arr);

console.log(arr.toString('base64url'));