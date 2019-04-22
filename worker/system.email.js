const nodemailer = require("nodemailer")

class Mail {
  constructor () {
    console.info('Create nodemailer transport ...')
    this.mailer = nodemailer.createTransport({
      pool: true,
      secure: true,
      host: process.env.VILOVEUL_SMTP_HOST,
      port: process.env.VILOVEUL_SMTP_PORT,
      auth: {
        user: process.env.VILOVEUL_SMTP_USERNAME,
        pass: process.env.VILOVEUL_SMTP_PASSWORD
      }
    });
  }

  async exec (params, args) {
    let initial = {
      email: undefined,
      subject: undefined,
      body: undefined
    }
    let data = Object.assign({}, initial, params)
    if (data.email !== undefined && data.subject !== undefined && data.body !== undefined) {
      let info = await this.mailer.sendMail({
        from: process.env.VILOVEUL_SMTP_NAME + ' <' + process.env.VILOVEUL_SMTP_USERNAME + '>',
        to: data.email,
        subject: data.subject,
        text: data.body,
        html: data.html === undefined ? data.body : data.html
      }).catch(e => {
        console.error(e)
      })
      console.log("Message sent: %s", info.messageId)
    }
  }
}

module.exports = new Mail()
