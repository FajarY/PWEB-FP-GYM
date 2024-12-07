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

test('Google OAuth URL Request [/api/auth/request]', async () =>
{
    const req =
    {
        method: "GET"
    };

    const res = await fetch(url + "api/auth/request", req);

    expect(res.status).toBe(utils.OK);
    expect(res).toHaveProperty('redirected');
    expect(res.redirected).toEqual(true);
    expect(res).toHaveProperty('url');
});

test('One time verify user [/api/auth/verify]', async () =>
{
    const req =
    {
        method: "POST",
        headers:
        {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            'username': 'API Test Username',
            'date_of_birth': new Date(Date.now()),
            'profile_image': fs.readFileSync('./tests/profile.png').toString('base64')
        })
    };
    if(use_cookie)
    {
        req.headers['Cookie'] = `token=${token};`;
    }
    else
    {
        req.headers['Authorization'] = `Bearer ${token}`;
    }

    const res = await fetch(url + 'api/auth/verify', req);

    if(res.status === utils.UNAUTHORIZED)
    {
        throw new Error('Token is not valid for testing, be sure to give valid token on .env.test. To get the token, go to /auth and sign in, open the cookie then copy and paste the token to .env.test');
    }
    expect(res.status).toBe(utils.OK);

    const data = await res.json();

    expect(data).toHaveProperty('succeed');
    expect(data.succeed).toEqual(true);
});