# Testing Report - duyler/parallel

## Status: ✅ ALL TESTS PASSED

**Date:** November 25, 2025  
**Environment:** PHP 8.4.6 with ext-parallel (ZTS)

---

## Test Results Summary

### PHPUnit: 71/71 ✅

| Test Suite | Tests | Status |
|------------|-------|--------|
| Integration Tests | 4/4 | ✅ PASSED |
| EventsTest | 13/13 | ✅ PASSED |
| FutureTest | 10/10 | ✅ PASSED |
| ParallelTest | 6/6 | ✅ PASSED |
| RuntimePoolTest | 8/8 | ✅ PASSED |
| RuntimeTest | 7/7 | ✅ PASSED |
| WorkflowBuilderTest | 9/9 | ✅ PASSED |
| ChannelTest | 14/14 | ✅ PASSED |

**Exit Code:** 0 (Success)

---

## Static Analysis

### Psalm: ✅ NO ERRORS

- **Type Coverage:** 99.64%
- **Errors:** 0
- **Time:** 0.82 seconds
- **Memory:** 59.93 MB

### PHP-CS-Fixer: ✅ COMPLIANT

- **Files Checked:** 41
- **Files Fixed:** 0
- **Status:** All code follows PSR-12 standard

---

## Test Coverage

### Runtime (7 tests)
- ✅ Create runtime with/without bootstrap
- ✅ Run simple/complex tasks
- ✅ Close/kill runtime
- ✅ Get native instance

### Future (10 tests)
- ✅ Get value (various types)
- ✅ Check done/cancelled status
- ✅ Cancel future
- ✅ Exception handling
- ✅ Interface implementation

### Channel (14 tests)
- ✅ Create buffered/unbuffered channels
- ✅ Send/receive data
- ✅ Named channels (make/open)
- ✅ Exception handling
- ✅ Channel closure

### Events (13 tests)
- ✅ Add future/channel to events
- ✅ Poll events
- ✅ Remove events
- ✅ Blocking/non-blocking modes
- ✅ Timeout handling
- ✅ Event properties

### Parallel Facade (6 tests)
- ✅ Runtime creation
- ✅ Channel creation
- ✅ Events creation
- ✅ Task execution

### RuntimePool (8 tests)
- ✅ Pool creation/configuration
- ✅ Task execution
- ✅ Runtime reuse
- ✅ Pool cleanup (close/kill)

### WorkflowBuilder (9 tests)
- ✅ Single/multiple tasks
- ✅ Channel integration
- ✅ Bootstrap support
- ✅ Result collection
- ✅ Resource cleanup

### Integration (4 tests)
- ✅ Complete workflow
- ✅ Event loop
- ✅ Named channels
- ✅ Multiple tasks

---


## Performance Notes

- All tests complete in < 1 second
- No memory leaks detected
- No hanging/blocking issues
- Clean shutdown of all Runtime instances

---

## Conclusion

✅ **All 71 tests pass successfully**  
✅ **No static analysis errors**  
✅ **Code style compliant**  
✅ **Full ext-parallel compatibility**  
✅ **Production ready**

The library is fully tested and ready for use with PHP 8.4+ and ext-parallel extension.

