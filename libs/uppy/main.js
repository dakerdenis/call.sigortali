const StatusBar = Uppy.StatusBar
        const Informer = Uppy.Informer
        const Webcam = Uppy.Webcam
        const Dashboard = Uppy.Dashboard
        const GoogleDrive = Uppy.GoogleDrive
        const Dropbox = Uppy.Dropbox
        const Instagram = Uppy.Instagram
        const Facebook = Uppy.Facebook
        const OneDrive = Uppy.OneDrive
        const ScreenCapture = Uppy.ScreenCapture
        const ImageEditor = Uppy.ImageEditor
        const Tus = Uppy.Tus
        const DropTarget = Uppy.DropTarget
        const GoldenRetriever = Uppy.GoldenRetriever
        const XHRUpload = Uppy.XHRUpload

        var uppy = new Uppy.Core({
            //id: 'uppy',
            autoProceed: false,
            allowMultipleUploads: true,
            debug: false,
            restrictions: {
            maxFileSize: null,
            minFileSize: null,
            maxTotalFileSize: null,
            maxNumberOfFiles: null,
            minNumberOfFiles: null,
            allowedFileTypes: ['image/*', 'video/*']
            },
            meta: {},
            onBeforeFileAdded: (currentFile, files) => currentFile,
            onBeforeUpload: (files) => {},
            //locale: Uppy.locales.ru_RU,
            // store: new DefaultStore(),
            // logger: justErrorsLogger,
            infoTimeout: 5000
        })

        .use(Dashboard, {
            trigger: '.UppyModalOpenerBtn',
            inline: true,
            target: '.uppy-container',
            replaceTargetContent: true,
            showProgressDetails: true,
            //note: 'Images and video only, 2–3 files, up to 1 MB',
            width: '100%',
            height: '400',
            // metaFields: [
            //     { id: 'name', name: 'Name', placeholder: 'file name' },
            //     { id: 'caption', name: 'Caption', placeholder: 'describe what the image is about' }
            // ],
            browserBackButtonClose: false,
            hideUploadButton: true
        })

        // .use(GoogleDrive, { target: Dashboard, companionUrl: 'https://companion.uppy.io' })
        // .use(Dropbox, { target: Dashboard, companionUrl: 'https://companion.uppy.io' })
        // .use(Instagram, { target: Dashboard, companionUrl: 'https://companion.uppy.io' })
        // .use(Facebook, { target: Dashboard, companionUrl: 'https://companion.uppy.io' })
        // .use(OneDrive, { target: Dashboard, companionUrl: 'https://companion.uppy.io' })
        .use(Webcam, { target: Dashboard })
        // .use(ScreenCapture, { target: Dashboard })
        .use(ImageEditor, { target: Dashboard })
        //.use(Tus, { endpoint: 'https://tusd.tusdemo.net/files/' })
        // .use(DropTarget, {target: document.body })
        // .use(GoldenRetriever)

        // uppy.use(Uppy.XHRUpload, {
        //     endpoint: 'upload_hanlder.php',
        //     formData: true,
        //     fieldName: 'files[]',
        // });

        // uppy.use(Uppy.Form, {
        //     target: '#formDataExample',
        //     resultName: 'uppyResult',
        //     getMetaFromForm: true,
        //     addResultToForm: true,
        //     submitOnSuccess: false,
        //     triggerUploadOnSubmit: true,
        // });

        // uppy.use(Uppy.AwsS3, {
        //     getUploadParameters (file) {
        //         // Send a request to our PHP signing endpoint.
        //         return fetch('/s3-sign.php', {
        //         method: 'post',
        //         // Send and receive JSON.
        //         headers: {
        //             accept: 'application/json',
        //             'content-type': 'application/json',
        //         },
        //         body: JSON.stringify({
        //             filename: file.name,
        //             contentType: file.type,
        //         }),
        //         }).then((response) => {
        //         // Parse the JSON response.
        //         return response.json()
        //         }).then((data) => {
        //         // Return an object in the correct shape.
        //         return {
        //             method: data.method,
        //             url: data.url,
        //             fields: data.fields,
        //             headers: data.headers,
        //         }
        //         })
        //     },
        // })

        // uppy.on("file-added", currentFile => {
        //     console.log("##### onFileAdded currentFile", currentFile);
        //     const file = uppy.getFile(currentFile.id);
        //     console.log("##### getFile file", file);
        //     return false;
        // });

        // uppy.on('file-editor:complete', (updatedFile) => {
        //     console.log(updatedFile);
        // });

        // uppy.on('complete', result => {
        //     document.location.reload();
        //     console.log('successful files:', result.successful)
        //     console.log('failed files:', result.failed)
        // })