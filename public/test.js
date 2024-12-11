import * as req from './requestTemplate.js';

const verify = document.getElementById('verify');
const me = document.getElementById('me');
const getExHeaders = document.getElementById('get-ex-header');
const image = document.getElementById('image');

verify.addEventListener('click', async () =>
{
    const file = image.files[0];
    if(file)
    {
        const reader = new FileReader();
        const blob = new Blob();
        reader.onload = async function(ev)
        {
            const result = ev.target.result;
            const base64String = result.split(',')[1];
            const data = await req.verify('Fajar', '2005-10-13', base64String);

            console.log(data[1]);
        }

        reader.readAsDataURL(file);
    }
});