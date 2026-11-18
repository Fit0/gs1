# 0003. Extracting the GS1 code from Estiba, and what changed

- Status: Accepted
- Date: 2026-11-18

## Context

Estiba WMS has the check digit, `Gtin` and `Sscc` as value objects of its domain. They were written for one application that validates its input before it gets there, and they throw the application's own domain errors (a stable code, translated by the app). This package is used by code that does not know any of that.

## Options considered

1. **Copy the classes as they are.** Fast, but a public library would inherit exceptions that need Estiba's error catalogue, a check digit that turns `"12a"` into a digit without a word, and an `Sscc` that refuses company prefixes by a range nobody can point to.
2. **Make Estiba depend on the package and delete its copy.** It would remove the duplicate. ADR 0001 decided against it: the flagship project should not need a satellite to build.
3. **Extract: copy the classes, harden them for public use, and give them their own tests.**

## Decision

Option 3. What changed, and why:

- **Exceptions are plain PHP.** Each one extends the SPL exception that fits (`InvalidArgumentException`, `OverflowException`) and implements `Fit0\Gs1\Exception\Gs1Exception`, so a caller can catch the whole package at once. There are no error codes or translations: the message is for a developer.
- **`Gs1CheckDigit::for()` rejects what is not digits.** Estiba's version cast every character to an int, so a letter weighed as 0. Now an empty value or anything but 0 to 9 throws `InvalidDigits`, and `Gs1CheckDigit::isValid()` answers the other question (does this number end in the right digit) without throwing.
- **The digit checks use `/^\d+$/D`.** Without `D`, `$` also matches before a final line break, so `"1234\n"` would pass.
- **`Sscc::compose()` checks the structure, not the licence.** Estiba required a company prefix of 6 to 10 digits, with no source behind the range. Sources disagree: GS1 UK describes prefixes of 7 to 10 digits and GS1 Australia 7 to 9, and the GS1 documents speak of a variable length. The package cannot know which prefixes GS1 has licensed to whom, so it only requires digits and room for at least one digit of serial (a prefix of up to 15 digits). The caller owns the rest.
- **The domain terms stay**: `Gtin`, `Sscc`, "normalized" for the 14-digit form, "company prefix", "extension digit" and "serial".
- **The test vectors are named for what backs them.** The four SSCCs come from a GS1 UK guide. The GTIN and UPC numbers are well known and agree with an independent implementation, but are not taken from a GS1 document, and the tests say so.

## Consequences

- The package and Estiba now differ on purpose. A fix in one is not a fix in the other, so a change to the check digit algorithm has to be made twice.
- A caller that relied on Estiba's narrower prefix range has to check it itself.
- Anything stricter than the standard says needs a source in the pull request.
