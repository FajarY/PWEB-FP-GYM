const OK = 200;
const CREATED = 201;
const ACCEPTED = 202;
const NO_CONTENT = 204;

const MOVED_PERMANENTLY = 301;
const FOUND = 302;
const NOT_MODIFIED = 304;

const BAD_REQUEST = 400;
const UNAUTHORIZED = 401;
const FORBIDDEN = 403;
const NOT_FOUND = 404;
const METHOD_NOT_ALLOWED = 405;
const CONFLICT = 409;
const UNPROCESSABLE_ENTITY = 422;

const INTERNAL_SERVER_ERROR = 500;
const NOT_IMPLEMENTED = 501;
const BAD_GATEWAY = 502;
const SERVICE_UNAVAILABLE = 503;
const GATEWAY_TIMEOUT = 504;

function getRandomString(length)
{
    const inputs = [
        '1', '2', '3', '4', '5', '6', '7', '8', '9', '0',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
        'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y', 'z'
    ];
    var randomString = '';
    for(var i = 0; i < length; i++)
    {
        randomString += inputs[Math.floor(Math.random() * inputs.length)];
    }

    return randomString;
}

module.exports = {
    OK,
    CREATED,
    ACCEPTED,
    NO_CONTENT,
    MOVED_PERMANENTLY,
    FOUND,
    NOT_MODIFIED,
    BAD_REQUEST,
    UNAUTHORIZED,
    FORBIDDEN,
    NOT_FOUND,
    METHOD_NOT_ALLOWED,
    CONFLICT,
    UNPROCESSABLE_ENTITY,
    INTERNAL_SERVER_ERROR,
    NOT_IMPLEMENTED,
    BAD_GATEWAY,
    SERVICE_UNAVAILABLE,
    GATEWAY_TIMEOUT,
    getRandomString
};