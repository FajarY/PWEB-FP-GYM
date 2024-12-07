const cfg = require('dotenv');
const fs = require('fs');
const utils = require('./template');

cfg.config(
    {
        path: '.env'
    }
);
cfg.config(
    {
        path: '.env.test'
    }
);

const url = process.env.url;
var token = process.env.token;
const use_cookie = process.env.use_cookie;

async function reset()
{
    const req =
    {
        method: "GET",
        headers:
        {
            'Content-Type':'application/json',
            'Authorization':`Bearer ${process.env.DEVELOPMENT_SECRET}`
        }
    };

    const res = await fetch(url + `api/reset`, req);

    const data = await res.json();
    console.log(data);

    if(res.status != utils.OK)
    {
        throw new Error("Error when resetting!");
    }
}

reset();